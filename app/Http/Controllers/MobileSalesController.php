<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendedor;
use App\VendedorSector;
use App\Sector;
use App\Zona;
use App\Clientes;
use App\Productos;
use App\Venta;
use App\Recibos;
use App\Creditos;
use App\Caja;
use App\Movimientos;
use App\StokLocation;
use App\Detalle_almacen_productos;
use App\Traslado;
use App\Detalle_traslado;
use App\StockVendedor;
use App\Almacen;
use App\Tipo_comprobantes;
use App\Tipo_documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\servicios\FuncionesController;
use Spatie\Permission\Models\Role;

class MobileSalesController extends Controller
{
    // Identifica los traslados de carga de stock a furgonetas. Se usa el `motivo`
    // (fijado por la propia app, siempre igual) en vez de la `serie` porque la serie
    // es configurable por sede en `correlativos` y puede no ser "CAR".
    const MOTIVO_CARGA_STOCK = 'CARGA DIARIA DE STOCK A MOVILES';

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if ($request->is('vendedor/*') || $request->is('vendedor')) {
                if ($user && !$user->esVendedorOCobrador()) {
                    abort(403, 'Acceso denegado. Este módulo es exclusivo para vendedores o cobradores.');
                }
            }
            return $next($request);
        });
    }

    private function resolveVendedor($usuario)
    {
        $vendedor = Vendedor::where('usuario_id', $usuario->id)->first();
        if (!$vendedor) {
            // Se crea el vendedor en la base de datos con valores por defecto para evitar restricciones NOT NULL
            $vendedor = Vendedor::create([
                'nombre' => $usuario->name,
                'documento' => '00000000', // DNI ficticio por defecto
                'direccion' => 'Dirección por defecto',
                'usuario_id' => $usuario->id,
                'estado' => 1,
            ]);
            
            // Buscar ubicación por defecto según la sede del usuario (furgoneta/stock principal)
            $ubicacion_id = null;
            $almacen = Almacen::where('sede_id', $usuario->sede_id)->first();
            if ($almacen) {
                $defaultUbicacion = DB::table('stock_location')
                    ->where('almacen_id', $almacen->id)
                    ->where('name', '!=', 'Transferencias')
                    ->orderByRaw("CASE WHEN name = 'Stock' THEN 1 ELSE 2 END")
                    ->first();
                if ($defaultUbicacion) {
                    $ubicacion_id = $defaultUbicacion->id;
                }
            }
            $vendedor->stock_location_id = $ubicacion_id;
            $vendedor->save();
        } else {
            if ($vendedor->estado != 1) {
                $vendedor->estado = 1;
                $vendedor->save();
            }
            // Si el vendedor de la BD no tiene ubicación asignada, le asignamos la principal de su sede
            if (!$vendedor->stock_location_id) {
                $ubicacion_id = null;
                $almacen = Almacen::where('sede_id', $usuario->sede_id)->first();
                if ($almacen) {
                    $defaultUbicacion = DB::table('stock_location')
                        ->where('almacen_id', $almacen->id)
                        ->where('name', '!=', 'Transferencias')
                        ->orderByRaw("CASE WHEN name = 'Stock' THEN 1 ELSE 2 END")
                        ->first();
                    if ($defaultUbicacion) {
                        $ubicacion_id = $defaultUbicacion->id;
                    }
                }
                $vendedor->stock_location_id = $ubicacion_id;
                $vendedor->save();
            }
        }
        return $vendedor;
    }

    /**
     * Verifica que el vendedor pertenezca a la sede indicada (vía su usuario),
     * para evitar que se consulte/liquide la caja de un vendedor de otra sede.
     */
    private function vendedorPerteneceASede(int $vendedorId, int $idsede): bool
    {
        $vendedor = Vendedor::with('usuario')->find($vendedorId);
        return $vendedor && $vendedor->usuario && (int) $vendedor->usuario->sede_id === $idsede;
    }

    /**
     * IDs de los roles Vendedor (a), Cobrador (a) y Cobrador (a) / Vendedor (a) —
     * cualquiera de los tres da acceso al módulo vendedor/cobrador.
     */
    private function rolesVendedorCobradorIds()
    {
        return Role::whereIn('name', ['VENDEDOR (a)', 'COBRADOR (a)', 'COBRADOR (a) / VENDEDOR (a)'])->pluck('id');
    }

    public function index()
    {
        return redirect()->route('vendedor.dashboard');
    }

    public function asignar()
    {
        return redirect()->action('MobileSalesController@asignarRutaIndex');
    }

    // ==========================================
    // SECCIÓN VENDEDOR (VISTA MÓVIL-FIRST)
    // ==========================================

    public function vendedorDashboard(Request $request)
    {
        $usuario = Auth::user();
        
        $vendedor = $this->resolveVendedor($usuario);

        // Sectores asignados para el día de hoy
        $fechaHoy = date('Y-m-d');
        $sectoresAsignados = VendedorSector::where('vendedor_id', $usuario->id)
                                           ->where('fecha', $fechaHoy)
                                           ->with('sector')
                                           ->get();

        // Acumulado de ventas pendientes de liquidación hoy
        $totalVentas = Venta::where('vendedor_id', $vendedor->id)
                            ->where('fecha', $fechaHoy)
                            ->where('estado_liquidacion', 'PENDIENTE')
                            ->sum('monto');

        // Acumulado de cobranzas pendientes de liquidación hoy
        $totalCobranzas = Recibos::where('vendedor_id', $vendedor->id)
                                 ->where('fech_rec', $fechaHoy)
                                 ->where('estado_liquidacion', 'PENDIENTE')
                                 ->sum('mont_rec');

        $cantVentas = Venta::where('vendedor_id', $vendedor->id)
                           ->where('fecha', $fechaHoy)
                           ->where('estado_liquidacion', 'PENDIENTE')
                           ->count();

        $cantCobranzas = Recibos::where('vendedor_id', $vendedor->id)
                                ->where('fech_rec', $fechaHoy)
                                ->where('estado_liquidacion', 'PENDIENTE')
                                ->count();

        // Calcular saldo total por cobrar de los clientes de los sectores de hoy
        $sectoresIds = $sectoresAsignados->pluck('sector_id')->toArray();
        $clientesIds = Clientes::where('estado_per', '=', '1')
                               ->whereIn('id_sector', $sectoresIds)
                               ->pluck('id')
                               ->toArray();

        $totalPorCobrar = 0;
        if (!empty($clientesIds)) {
            $totalPorCobrar = DB::table('cuotas as cu')
                ->join('creditos as c', 'cu.credito_id', '=', 'c.id')
                ->where('c.esta_cre', '=', '1')
                ->whereIn('c.cliente_id', $clientesIds)
                ->where('cu.esta_cuo', '=', 'PENDIENTE')
                ->sum('cu.saldo_cuo') ?? 0;
        }

        // Calcular stock actual en la furgoneta (productos cargados HOY a este vendedor)
        $idsede = session('key')->sede_id;
        $almacenPrincipal = \App\Almacen::where('sede_id', $idsede)->first();
        $ubicacionMoviles = DB::table('stock_location')
                                ->where('almacen_id', $almacenPrincipal->id)
                                ->where(DB::raw('LOWER(name)'), 'moviles')
                                ->first();
        $ubicacion_id = $ubicacionMoviles ? $ubicacionMoviles->id : null;

        $totalStockItems = 0;
        $totalStockUnits = 0;

        if ($ubicacion_id) {
            $servicios = new FuncionesController;
            $envio = $servicios->tipo_envio_sunat();

            // Cargado HOY a este vendedor, agrupado por producto
            $loadedByProduct = DB::table('detalle_traslado as dt')
                ->join('traslados as t', 't.id', '=', 'dt.traslado_id')
                ->where('t.motivo', '=', self::MOTIVO_CARGA_STOCK)
                ->where('t.cliente_id', '=', $usuario->id)
                ->where('t.fecha', '=', $fechaHoy)
                ->where('t.estado', '=', 1)
                ->where('dt.estado', '=', 1)
                ->groupBy('dt.producto_id')
                ->select('dt.producto_id', DB::raw('SUM(dt.cantidad) as total'))
                ->pluck('total', 'dt.producto_id')
                ->toArray();

            // Vendido HOY por este vendedor, agrupado por producto (solo ventas activas)
            $soldByProduct = [];
            if ($vendedor) {
                $soldByProduct = DB::table('detalle_venta as dv')
                    ->join('ventas as v', 'v.id', '=', 'dv.venta_id')
                    ->where('v.vendedor_id', '=', $vendedor->id)
                    ->where('v.fecha', '=', $fechaHoy)
                    ->where('v.venta_estado', '=', 1)
                    ->where('v.tipo_envio', '=', $envio)
                    ->groupBy('dv.producto_id')
                    ->select('dv.producto_id', DB::raw('SUM(dv.cantidad) as total'))
                    ->pluck('total', 'dv.producto_id')
                    ->toArray();
            }

            // Stock neto real por producto = cargado - vendido
            $totalStockUnits = 0;
            $totalStockItems = 0;
            foreach ($loadedByProduct as $prodId => $loadedQty) {
                $soldQty = (int) ($soldByProduct[$prodId] ?? 0);
                $net = max(0, (int) $loadedQty - $soldQty);
                $totalStockUnits += $net;
                if ($net > 0) {
                    $totalStockItems++;
                }
            }
        }

        return view('ventas_moviles.dashboard', compact(
            'vendedor', 
            'sectoresAsignados', 
            'totalVentas', 
            'totalCobranzas', 
            'cantVentas', 
            'cantCobranzas',
            'totalPorCobrar',
            'totalStockUnits',
            'totalStockItems'
        ));
    }

    public function vendedorVenta(Request $request)
    {
        $usuario = Auth::user();
        $vendedor = $this->resolveVendedor($usuario);

        $idsede = session('key')->sede_id;
        $almacenPrincipal = \App\Almacen::where('sede_id', $idsede)->first();
        $ubicacionMoviles = DB::table('stock_location')
                                ->where('almacen_id', $almacenPrincipal->id)
                                ->where(DB::raw('LOWER(name)'), 'moviles')
                                ->first();

        // Ubicación de la furgoneta / moviles
        $ubicacion_id = $ubicacionMoviles ? $ubicacionMoviles->id : null;
        if (!$ubicacion_id) {
            return redirect()->back()->with('error', 'No se encontró la ubicación de stock "moviles".');
        }

        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        // Productos cargados HOY a la furgoneta de este vendedor (de stock_vendedor)
        $fechaHoy = date('Y-m-d');
        $productos = DB::table('stock_vendedor as sv')
            ->join('productos as p', 'p.id', '=', 'sv.producto_id')
            ->leftJoin('precios as pr', 'pr.articulo_id', '=', 'p.id')
            ->select(
                'p.id',
                'p.nomb_pro',
                DB::raw('SUM(sv.cantidad_disponible) as stock'),
                'pr.precio_contado',
                'pr.precio_credito'
            )
            ->where('sv.vendedor_id', '=', $usuario->id)
            ->where('sv.fecha_carga', '=', $fechaHoy)
            ->where('sv.estado', '=', 1) // solo activos
            ->where('p.estado', '=', '1')
            ->groupBy('p.id', 'p.nomb_pro', 'pr.precio_contado', 'pr.precio_credito')
            ->orderBy('p.nomb_pro', 'asc')
            ->get();

        // Filtrar solo productos con stock disponible > 0
        $productos = $productos->filter(function($p) { return $p->stock > 0; })->values();

        // Clientes de los sectores asignados para hoy
        $sectoresIds = VendedorSector::where('vendedor_id', $usuario->id)
                                    ->where('fecha', $fechaHoy)
                                    ->pluck('sector_id')
                                    ->toArray();

        $clientes = Clientes::where('estado_per', '=', '1')
                            ->whereIn('id_sector', $sectoresIds)
                            ->get();

        // Tipos de comprobante
        $comprobantes = Tipo_comprobantes::whereIn('id', [1, 2, 5])->orderBy('id', 'asc')->get(); // Boleta, Factura, Nota de Venta

        // Documentos de identidad
        $tipo_documento = Tipo_documento::all();

        // Formas de pago y bancos
        $forma_pagos = DB::table('forma_pagos')->orderBy('id', 'asc')->get();
        $bancos = DB::table('cuentas_bancarias as cb')
            ->join('bancos as b', 'b.id', '=', 'cb.banco_id')
            ->select('cb.id', 'cb.cuenta_corriente', 'b.abreviatura')
            ->get();
        
        $sectores = Sector::with('zona')->where('estado', 'ACTIVO')->orderBy('zona_id')->get();

        return view('ventas_moviles.venta', compact(
            'vendedor',
            'usuario',
            'productos',
            'clientes',
            'comprobantes',
            'tipo_documento',
            'ubicacion_id',
            'forma_pagos',
            'bancos',
            'sectores'
        ));
    }


    public function vendedorVentaGuardar(Request $request)
    {
        // Forzar es_movil a true
        $request->merge(['es_movil' => true]);

        // Reutilizar la lógica de venta existente para mantener la integridad de la base de datos
        $ventaController = new VentaController;
        return $ventaController->generar_venta($request);
    }

    public function vendedorCreditoParametros(Request $request)
    {
        // Obtener parámetros de crédito definidos por la empresa (candados)
        $candados = DB::table('candados')
            ->where('estado', 1)
            ->orderBy('rango_minimo', 'asc')
            ->get(['rango_minimo', 'rango_maximo', 'nmeses', 'monto_inicial']);

        // Obtener conceptos de crédito
        $conceptos = DB::table('concepto_credito')
            ->where('estado', 1)
            ->get(['id', 'name']);

        return response()->json([
            'candados' => $candados,
            'conceptos' => $conceptos
        ]);
    }

    public function vendedorCobros(Request $request)
    {
        $usuario = Auth::user();
        $vendedor = $this->resolveVendedor($usuario);

        // Sectores del vendedor hoy
        $fechaHoy = date('Y-m-d');
        $sectoresIds = VendedorSector::where('vendedor_id', $usuario->id)
                                    ->where('fecha', $fechaHoy)
                                    ->pluck('sector_id')
                                    ->toArray();

        // Clientes de dichos sectores
        $clientesIds = Clientes::where('estado_per', '=', '1')
                               ->whereIn('id_sector', $sectoresIds)
                               ->pluck('id')
                               ->toArray();

        // Créditos activos de los clientes en ruta
        $creditos = DB::table('creditos as c')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            ->select(
                'c.id',
                'c.cliente_id',
                'cl.razon_social',
                'cl.documento',
                'c.impo_cre',
                DB::raw("(SELECT COALESCE(SUM(saldo_cuo), 0) FROM cuotas WHERE credito_id = c.id AND esta_cuo = 'PENDIENTE') as saldo_pendiente")
            )
            ->where('c.esta_cre', '=', '1')
            ->whereIn('c.cliente_id', $clientesIds)
            ->get();

        return view('ventas_moviles.cobros', compact('vendedor', 'creditos'));
    }

    public function vendedorCobrosGuardar(Request $request)
    {
        // Forzar es_movil a true
        $request->merge(['es_movil' => true]);

        // Reutilizar la lógica de amortización existente
        $amortizacionesController = new AmortizacionesController;
        return $amortizacionesController->crear($request);
    }

    public function vendedorStock(Request $request)
    {
        $usuario = Auth::user();
        $vendedor = $this->resolveVendedor($usuario);

        $idsede = session('key')->sede_id;
        $almacenPrincipal = \App\Almacen::where('sede_id', $idsede)->first();
        $ubicacionMoviles = DB::table('stock_location')
                                ->where('almacen_id', $almacenPrincipal->id)
                                ->where(DB::raw('LOWER(name)'), 'moviles')
                                ->first();

        $ubicacion_id = $ubicacionMoviles ? $ubicacionMoviles->id : null;
        if (!$ubicacion_id) {
            return redirect()->back()->with('error', 'No se encontró la ubicación de stock "moviles".');
        }

        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $stock = DB::table('detalle_almacen_productos as dp')
            ->join('productos as p', 'dp.producto_id', '=', 'p.id')
            ->select('p.nomb_pro', 'dp.stock')
            ->where('dp.ubicacion_id', '=', $ubicacion_id)
            ->where('dp.tipo_envio', '=', $envio)
            ->where('p.estado', '=', '1')
            ->get();

        return view('ventas_moviles.stock', compact('vendedor', 'stock'));
    }


    // ==========================================
    // SECCIÓN ADMINISTRATIVA (ESCRITORIO)
    // ==========================================

    /**
     * Consultas agregadas (una por tabla, sin N+1) de ventas contado/crédito,
     * inicial de crédito (venta_formapago) y cobranzas pendientes de liquidar,
     * agrupadas por vendedor, para la fecha dada.
     */
    private function resumenVendedoresPendientes(array $vendedorIds, string $fecha): array
    {
        $ventasPorVendedor = DB::table('ventas')
            ->select('vendedor_id', 'tipo_pago_id', DB::raw('SUM(monto) as total'))
            ->whereIn('vendedor_id', $vendedorIds)
            ->where('estado_liquidacion', 'PENDIENTE')
            ->where('fecha', $fecha)
            ->groupBy('vendedor_id', 'tipo_pago_id')
            ->get()
            ->groupBy('vendedor_id');

        // Cuota inicial de ventas al crédito: dinero cobrado en efectivo al momento
        // de la venta, guardado en venta_formapago (no en recibos).
        $inicialPorVendedor = DB::table('venta_formapago as vf')
            ->join('ventas as v', 'v.id', '=', 'vf.venta_id')
            ->select('v.vendedor_id', DB::raw('SUM(vf.monto) as total'))
            ->whereIn('v.vendedor_id', $vendedorIds)
            ->where('v.estado_liquidacion', 'PENDIENTE')
            ->where('v.fecha', $fecha)
            ->where('v.tipo_pago_id', 2)
            ->groupBy('v.vendedor_id')
            ->get()
            ->keyBy('vendedor_id');

        $cobrosPorVendedor = DB::table('recibos')
            ->select('vendedor_id', DB::raw('SUM(mont_rec) as total'))
            ->whereIn('vendedor_id', $vendedorIds)
            ->where('estado_liquidacion', 'PENDIENTE')
            ->where('fech_rec', $fecha)
            ->groupBy('vendedor_id')
            ->get()
            ->keyBy('vendedor_id');

        return compact('ventasPorVendedor', 'inicialPorVendedor', 'cobrosPorVendedor');
    }

    /**
     * Total pendiente por vendedor y por forma de pago (Efectivo/Yape/etc.), sumando
     * lo cobrado en ventas al contado + inicial de crédito (venta_formapago) y
     * cobranzas (recibos, vía movimientos). Devuelve [vendedor_id => [forma_pago_id => monto]].
     */
    private function resumenFormaPagoPendiente(array $vendedorIds, string $fecha): array
    {
        $porVenta = DB::table('venta_formapago as vf')
            ->join('ventas as v', 'v.id', '=', 'vf.venta_id')
            ->select('v.vendedor_id', 'vf.forma_pago_id', DB::raw('SUM(vf.monto) as total'))
            ->whereIn('v.vendedor_id', $vendedorIds)
            ->where('v.estado_liquidacion', 'PENDIENTE')
            ->where('v.fecha', $fecha)
            ->groupBy('v.vendedor_id', 'vf.forma_pago_id')
            ->get();

        $porCobros = DB::table('recibos as r')
            ->leftJoin('movimientos as m', 'm.id', '=', 'r.id_movimiento')
            ->select('r.vendedor_id', 'm.forma_pago_id', DB::raw('SUM(r.mont_rec) as total'))
            ->whereIn('r.vendedor_id', $vendedorIds)
            ->where('r.estado_liquidacion', 'PENDIENTE')
            ->where('r.fech_rec', $fecha)
            ->groupBy('r.vendedor_id', 'm.forma_pago_id')
            ->get();

        $porVendedor = [];
        foreach ($porVenta->concat($porCobros) as $fila) {
            // forma_pago_id null (movimiento sin forma de pago asociada) se agrupa
            // bajo la clave 0 ("Sin especificar") para no perder el monto del total.
            $fpId = $fila->forma_pago_id ?? 0;
            $porVendedor[$fila->vendedor_id][$fpId] = ($porVendedor[$fila->vendedor_id][$fpId] ?? 0) + (float) $fila->total;
        }

        return $porVendedor;
    }

    /**
     * Convierte un array [forma_pago_id => monto] en una lista lista para mostrar,
     * con la descripción de cada forma de pago (0 = "Sin especificar").
     */
    private function listaResumenFormaPago(array $totalesPorForma, $formasPago): array
    {
        $lista = $formasPago->map(function ($fp) use ($totalesPorForma) {
            return [
                'forma_pago_id' => $fp->id,
                'descripcion' => $fp->descripcion,
                'monto' => round($totalesPorForma[$fp->id] ?? 0, 2),
            ];
        })->values()->all();

        if (!empty($totalesPorForma[0])) {
            $lista[] = ['forma_pago_id' => 0, 'descripcion' => 'Sin especificar', 'monto' => round($totalesPorForma[0], 2)];
        }

        return $lista;
    }

    /**
     * Query de las filas de inicial de crédito pendientes de un vendedor/fecha,
     * con nombre de cliente y comprobante, para el detalle del modal.
     */
    private function inicialesPendientes(int $vendedorId, string $fecha)
    {
        return DB::table('venta_formapago as vf')
            ->join('ventas as v', 'v.id', '=', 'vf.venta_id')
            ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            ->leftJoin('forma_pagos as fp', 'fp.id', '=', 'vf.forma_pago_id')
            ->select(
                'v.id',
                'v.fecha',
                'v.serie_comprobante',
                'v.numero_comprobante',
                'vf.monto',
                DB::raw("COALESCE(c.razon_social, c.nomb_per, 'Cliente Desconocido') as nombre_cliente"),
                'fp.descripcion as forma_pago'
            )
            ->where('v.vendedor_id', $vendedorId)
            ->where('v.estado_liquidacion', 'PENDIENTE')
            ->where('v.fecha', $fecha)
            ->where('v.tipo_pago_id', 2)
            ->orderBy('v.fecha', 'desc')
            ->get();
    }

    // A. Liquidación de caja en efectivo
    public function liquidarCajaIndex(Request $request)
    {
        $fecha = $request->input('fecha', date('Y-m-d'));
        $idsede = session('key')->sede_id;

        // Detalle de productos de una venta puntual (para el modal "Ver productos"
        // del tab Ventas Pendientes, tanto contado como crédito).
        if ($request->input('format') == 'json' && $request->filled('venta_id')) {
            $ventaId = $request->venta_id;
            $ventaValida = DB::table('ventas')->where('id', $ventaId)->where('sede_id', $idsede)->exists();
            if (!$ventaValida) {
                abort(403, 'No autorizado para ver esta venta.');
            }

            $productos = DB::table('detalle_venta as dv')
                ->join('productos as p', 'p.id', '=', 'dv.producto_id')
                ->select('p.nomb_pro as nombre', 'dv.cantidad', 'dv.precio', 'dv.subtotal')
                ->where('dv.venta_id', $ventaId)
                ->orderBy('p.nomb_pro')
                ->get();

            return response()->json(['productos' => $productos]);
        }

        if ($request->input('format') == 'json' && $request->vendedor_id) {
            $vendedorId = $request->vendedor_id;

            if (!$this->vendedorPerteneceASede((int) $vendedorId, (int) $idsede)) {
                abort(403, 'No autorizado para ver la caja de este vendedor.');
            }

            // Ventas con nombre del cliente (join explícito)
            $ventas = DB::table('ventas as v')
                ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
                ->leftJoin('tipo_comprobantes as tc', 'v.tipo_comprobante_id', '=', 'tc.id')
                ->select(
                    'v.id',
                    'v.fecha',
                    'v.serie_comprobante',
                    'v.numero_comprobante',
                    'v.monto',
                    'v.tipo_pago_id',
                    'v.estado_liquidacion',
                    DB::raw("COALESCE(c.razon_social, c.nomb_per, 'Cliente Desconocido') as nombre_cliente"),
                    'tc.descripcion as tipo_comprobante',
                    // La venta al crédito en sí no tiene forma de pago (solo la cuota
                    // inicial, si existe, que ya se muestra en su propio tab con su
                    // propio monto) — mostrarla aquí junto al monto total del crédito
                    // sugeriría erróneamente que toda la venta se cobró así.
                    DB::raw("CASE WHEN v.tipo_pago_id = 2 THEN NULL ELSE
                        (SELECT fp.descripcion FROM venta_formapago vf JOIN forma_pagos fp ON fp.id = vf.forma_pago_id WHERE vf.venta_id = v.id ORDER BY vf.id LIMIT 1)
                        END as forma_pago")
                )
                ->where('v.vendedor_id', $vendedorId)
                ->where('v.estado_liquidacion', 'PENDIENTE')
                ->where('v.fecha', $fecha)
                ->orderBy('v.fecha', 'desc')
                ->get();

            // Cobros (recibos) con nombre del cliente (join explícito)
            // Nota: la forma de pago de un recibo NO se lee de recibos.fpag_rec (esa
            // columna es la fecha de pago, no un id pese al nombre) sino vía
            // recibos.id_movimiento -> movimientos.forma_pago_id -> forma_pagos.id.
            $cobros = DB::table('recibos as r')
                ->join('clientes as c', 'r.cliente_id', '=', 'c.id')
                ->leftJoin('movimientos as m', 'm.id', '=', 'r.id_movimiento')
                ->leftJoin('forma_pagos as fp', 'fp.id', '=', 'm.forma_pago_id')
                ->select(
                    'r.id',
                    'r.fech_rec',
                    'r.num_recibo',
                    'r.mont_rec',
                    'r.docu_ref',
                    'r.estado_liquidacion',
                    DB::raw("COALESCE(c.razon_social, c.nomb_per, 'Cliente Desconocido') as nombre_cliente"),
                    'fp.descripcion as forma_pago'
                )
                ->where('r.vendedor_id', $vendedorId)
                ->where('r.estado_liquidacion', 'PENDIENTE')
                ->where('r.fech_rec', $fecha)
                ->orderBy('r.fech_rec', 'desc')
                ->get();

            $iniciales = $this->inicialesPendientes((int) $vendedorId, $fecha);

            $formasPago = DB::table('forma_pagos')->orderBy('id')->get(['id', 'descripcion']);
            $resumenPorVendedor = $this->resumenFormaPagoPendiente([(int) $vendedorId], $fecha);
            $resumenFormaPago = $this->listaResumenFormaPago($resumenPorVendedor[(int) $vendedorId] ?? [], $formasPago);

            return response()->json([
                'ventas' => $ventas,
                'cobros' => $cobros,
                'iniciales' => $iniciales,
                'resumen_forma_pago' => $resumenFormaPago,
            ]);
        }

        // Obtener usuarios activos de esta sede con rol vendedor/cobrador
        $rolesIds = $this->rolesVendedorCobradorIds();
        $usuariosVendedoresIds = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function($q) use ($rolesIds) {
                $q->whereIn('id', $rolesIds);
            })->pluck('id');

        $vendedores = Vendedor::with('stockLocation')
                              ->where('estado', 1)
                              ->whereIn('usuario_id', $usuariosVendedoresIds)
                              ->get();

        $vendedorIds = $vendedores->pluck('id')->all();

        [
            'ventasPorVendedor' => $ventasPorVendedor,
            'inicialPorVendedor' => $inicialPorVendedor,
            'cobrosPorVendedor' => $cobrosPorVendedor,
        ] = $this->resumenVendedoresPendientes($vendedorIds, $fecha);

        $formasPago = DB::table('forma_pagos')->orderBy('id')->get(['id', 'descripcion']);
        $resumenFormaPagoPorVendedor = $this->resumenFormaPagoPendiente($vendedorIds, $fecha);
        $resumenFormaPagoGeneralTotales = [];
        foreach ($resumenFormaPagoPorVendedor as $porForma) {
            foreach ($porForma as $fpId => $monto) {
                $resumenFormaPagoGeneralTotales[$fpId] = ($resumenFormaPagoGeneralTotales[$fpId] ?? 0) + $monto;
            }
        }
        $resumenFormaPagoGeneral = $this->listaResumenFormaPago($resumenFormaPagoGeneralTotales, $formasPago);

        $totalGralEfectivo = 0;
        $totalGralVentasContado = 0;
        $totalGralVentasCredito = 0;
        $totalGralInicialCredito = 0;
        $totalGralCobranzas = 0;

        foreach ($vendedores as $v) {
            $filasVentas = $ventasPorVendedor->get($v->id, collect());
            $v->total_ventas_contado = (float) $filasVentas->where('tipo_pago_id', 1)->sum('total');
            $v->total_ventas_credito = (float) $filasVentas->where('tipo_pago_id', 2)->sum('total');
            $v->total_inicial_credito = (float) ($inicialPorVendedor->get($v->id)->total ?? 0);
            $v->total_cobros_credito = (float) ($cobrosPorVendedor->get($v->id)->total ?? 0);

            $v->total_efectivo_pendiente = $v->total_ventas_contado + $v->total_inicial_credito + $v->total_cobros_credito;
            $v->tiene_pendiente = $v->total_efectivo_pendiente > 0 || $v->total_ventas_credito > 0;

            // Totales acumulados generales
            $totalGralEfectivo += $v->total_efectivo_pendiente;
            $totalGralVentasContado += $v->total_ventas_contado;
            $totalGralVentasCredito += $v->total_ventas_credito;
            $totalGralInicialCredito += $v->total_inicial_credito;
            $totalGralCobranzas += $v->total_cobros_credito;
        }

        if ($request->input('format') == 'json') {
            return response()->json([
                'fecha' => $fecha,
                'totales' => [
                    'efectivo' => $totalGralEfectivo,
                    'ventas_contado' => $totalGralVentasContado,
                    'ventas_credito' => $totalGralVentasCredito,
                    'inicial_credito' => $totalGralInicialCredito,
                    'cobranzas' => $totalGralCobranzas,
                ],
                'resumen_forma_pago' => $resumenFormaPagoGeneral,
                'vendedores' => $vendedores->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'nombre' => $v->nombre,
                        'furgoneta' => optional($v->stockLocation)->name,
                        'total_ventas_contado' => $v->total_ventas_contado,
                        'total_ventas_credito' => $v->total_ventas_credito,
                        'total_inicial_credito' => $v->total_inicial_credito,
                        'total_cobros_credito' => $v->total_cobros_credito,
                        'total_efectivo_pendiente' => $v->total_efectivo_pendiente,
                        'tiene_pendiente' => $v->tiene_pendiente,
                    ];
                })->values(),
            ]);
        }

        return view('ventas_moviles.liquidar', compact(
            'vendedores',
            'totalGralEfectivo',
            'totalGralVentasContado',
            'totalGralVentasCredito',
            'totalGralInicialCredito',
            'totalGralCobranzas',
            'resumenFormaPagoGeneral',
            'fecha'
        ));
    }

    public function liquidarCajaProcesar(Request $request)
    {
        $vendedorId = $request->vendedor_id;
        if (!$vendedorId) {
            return redirect()->back()->with('error', 'Debe seleccionar un vendedor.');
        }

        $idsede = session('key')->sede_id;
        if (!$this->vendedorPerteneceASede((int) $vendedorId, (int) $idsede)) {
            abort(403, 'No autorizado para liquidar la caja de este vendedor.');
        }

        $fecha = $request->input('fecha', date('Y-m-d'));

        DB::beginTransaction();

        try {
            $user_id = Auth::user()->id;
            $servicios = new FuncionesController;
            $envio = $servicios->tipo_envio_sunat();

            // Buscar caja abierta del cajero/administrador que liquida
            $caja = Caja::where('user_id', '=', $user_id)
                        ->where('tipo_envio', '=', $envio)
                        ->where('sede_id', '=', $idsede)
                        ->orderBy('id', 'desc')
                        ->first();

            if (!$caja) {
                return redirect()->back()->with('error', 'Debe abrir su caja de cajero/administrador antes de realizar liquidaciones.');
            }

            // 1. Procesar Ventas
            $ventas = Venta::where('vendedor_id', $vendedorId)
                           ->where('estado_liquidacion', 'PENDIENTE')
                           ->where('fecha', $fecha)
                           ->get();

            foreach ($ventas as $venta) {
                $ventaFormapago = DB::table('venta_formapago')->where('venta_id', $venta->id)->get();
                foreach ($ventaFormapago as $fp) {
                    DB::table('movimientos')->where('id', $fp->movimiento_id)->update([
                        'estado' => 1,
                        'id_sesion_caja' => $caja->id,
                        'fecha' => date('Y-m-d'),
                        'hora' => date('H:i:s')
                    ]);
                }
                $venta->estado_liquidacion = 'LIQUIDADO';
                $venta->save();

                // Si la venta es al crédito y tiene cuota inicial (numero 0), marcarla como COBRADA
                $credito = \App\Creditos::where('id_venta', $venta->id)->first();
                if ($credito) {
                    $cuotaInicial = \App\Cuotas::where('credito_id', $credito->id)
                        ->where('numero_cuo', 0)
                        ->first();
                    if ($cuotaInicial && $cuotaInicial->esta_cuo == 'PENDIENTE') {
                        $cuotaInicial->saldo_cuo = 0.00;
                        $cuotaInicial->sald_cap = 0.00;
                        $cuotaInicial->capi_cuo = 0.00;
                        $cuotaInicial->esta_cuo = 'COBRADA';
                        $cuotaInicial->save();
                    }
                }
            }

            // 2. Procesar Amortizaciones (Recibos)
            $recibos = Recibos::where('vendedor_id', $vendedorId)
                              ->where('estado_liquidacion', 'PENDIENTE')
                              ->where('fech_rec', $fecha)
                              ->get();

            foreach ($recibos as $recibo) {
                if ($recibo->id_movimiento) {
                    DB::table('movimientos')->where('id', $recibo->id_movimiento)->update([
                        'estado' => 1,
                        'id_sesion_caja' => $caja->id,
                        'fecha' => date('Y-m-d'),
                        'hora' => date('H:i:s')
                    ]);
                }
                $recibo->estado_liquidacion = 'LIQUIDADO';
                $recibo->save();
            }

            DB::commit();
            return redirect()->route('admin.liquidar')->with('success', 'La caja del vendedor fue liquidada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al liquidar caja', [
                'vendedor_id' => $vendedorId,
                'fecha' => $fecha,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'No se pudo liquidar la caja. Intente nuevamente o contacte a soporte.');
        }
    }

    // B. Reconciliación y Retorno de Stock
    public function retornoStockIndex(Request $request)
    {
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $fecha = $request->input('fecha', date('Y-m-d'));

        $idsede = session('key')->sede_id;
        $almacenPrincipal = \App\Almacen::where('sede_id', $idsede)->first();
        if (!$almacenPrincipal) {
            return redirect()->route('admin.liquidar')->with('error', 'No se encontró el almacén principal de la sede. Contacte a soporte.');
        }
        $ubicacionMoviles = DB::table('stock_location')
                                ->where('almacen_id', $almacenPrincipal->id)
                                ->where(DB::raw('LOWER(name)'), 'moviles')
                                ->first();

        $moviles_id = $ubicacionMoviles ? $ubicacionMoviles->id : null;

        if ($request->input('format') == 'json' && $request->vendedor_id) {
            // vendedor_id aquí es users.id (igual que en cargarStockIndex/historial-cargas),
            // no vendedores.id -- la tabla vendedores ya no es fuente de verdad de esto.
            $perteneceASede = \App\User::where('id', $request->vendedor_id)
                ->where('sede_id', $idsede)
                ->exists();
            if (!$perteneceASede) {
                abort(403, 'El vendedor no pertenece a su sede.');
            }
            $stock = DB::table('stock_vendedor as sv')
                ->join('productos as p', 'p.id', '=', 'sv.producto_id')
                ->select(
                    'p.id',
                    'p.nomb_pro',
                    DB::raw('SUM(sv.cantidad_disponible) as stock'),
                    DB::raw('SUM(sv.cantidad_cargada) as cargado'),
                    DB::raw('SUM(sv.cantidad_vendida) as vendido')
                )
                ->where('sv.vendedor_id', '=', $request->vendedor_id)
                ->where('sv.fecha_carga', '=', $fecha)
                ->where('sv.estado', '=', 1)
                ->where('p.estado', '=', '1')
                ->groupBy('p.id', 'p.nomb_pro')
                ->havingRaw('SUM(sv.cantidad_cargada) > 0')
                ->get();
            return response()->json($stock);
        }

        // Obtener sede activa de la sesión
        $idsede = session('key')->sede_id;

        // Obtener usuarios activos de esta sede con rol vendedor/cobrador.
        // Fuente de verdad: users + rol (igual que cargarStockIndex/historial-cargas).
        // La tabla vendedores ya no se mantiene como negocio, así que solo se usa
        // acá como dato complementario opcional (nombre de furgoneta), sin excluir
        // a nadie si la fila falta, está desactualizada o sin stock_location_id.
        $rolesIds = $this->rolesVendedorCobradorIds();
        $usuarios = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function($q) use ($rolesIds) {
                $q->whereIn('id', $rolesIds);
            })->orderBy('name', 'asc')->get();

        $usuarioIds = $usuarios->pluck('id')->all();

        $furgonetasPorUsuario = Vendedor::with('stockLocation')
            ->whereIn('usuario_id', $usuarioIds)
            ->get()
            ->keyBy('usuario_id');

        $stockPorUsuarioYProducto = DB::table('stock_vendedor as sv')
            ->join('productos as p', 'p.id', '=', 'sv.producto_id')
            ->select('sv.vendedor_id', 'sv.producto_id', DB::raw('SUM(sv.cantidad_disponible) as stock'))
            ->whereIn('sv.vendedor_id', $usuarioIds)
            ->where('sv.fecha_carga', '=', $fecha)
            ->where('sv.estado', '=', 1)
            ->where('p.estado', '=', '1')
            ->groupBy('sv.vendedor_id', 'sv.producto_id')
            ->havingRaw('SUM(sv.cantidad_disponible) > 0')
            ->get()
            ->groupBy('vendedor_id');

        $vendedores = $usuarios->map(function ($u) use ($stockPorUsuarioYProducto, $furgonetasPorUsuario) {
            $filas = $stockPorUsuarioYProducto->get($u->id, collect());
            $furgoneta = $furgonetasPorUsuario->get($u->id);
            return (object) [
                'id' => $u->id, // users.id: identidad usada en todo este flujo
                'nombre' => $u->name,
                'stockLocation' => optional($furgoneta)->stockLocation,
                'total_items' => $filas->count(),
                'total_unidades' => (float) $filas->sum('stock'),
            ];
        });

        if ($request->input('format') == 'json') {
            return response()->json([
                'fecha' => $fecha,
                'vendedores' => $vendedores->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'nombre' => $v->nombre,
                        'furgoneta' => $v->stockLocation->name ?? 'Sin furgoneta',
                        'total_items' => $v->total_items,
                        'total_unidades' => $v->total_unidades,
                    ];
                })->values(),
            ]);
        }

        return view('ventas_moviles.retorno', compact('vendedores', 'fecha'));
    }

    public function retornoStockProcesar(Request $request)
    {
        $vendedorId = $request->vendedor_id;
        $productosIds = $request->productos; // array
        $fisicoRecibido = $request->fisico; // array de producto_id => cantidad_fisica
        $fecha = $request->input('fecha', date('Y-m-d')); // fecha de carga que se está reconciliando

        if (!$vendedorId || empty($productosIds)) {
            return redirect()->back()->with('error', 'Debe seleccionar un vendedor y al menos un producto.');
        }

        $idsede = session('key')->sede_id;

        // vendedor_id es users.id (ver nota en retornoStockIndex): ya no se usa vendedores.id.
        $vendedor = \App\User::where('id', $vendedorId)
            ->where('sede_id', $idsede)
            ->first();
        if (!$vendedor) {
            abort(403, 'El vendedor no pertenece a su sede.');
        }

        $almacenPrincipal = \App\Almacen::where('sede_id', $idsede)->first();
        if (!$almacenPrincipal) {
            return redirect()->back()->with('error', 'No se encontró el almacén principal de la sede.');
        }
        $ubicacionMoviles = DB::table('stock_location')
                                ->where('almacen_id', $almacenPrincipal->id)
                                ->where(DB::raw('LOWER(name)'), 'moviles')
                                ->first();

        $origen_id = $ubicacionMoviles ? $ubicacionMoviles->id : null;

        if (!$origen_id) {
            return redirect()->back()->with('error', 'No se encontró la ubicación de stock "moviles".');
        }

        // Validación server-side: el físico recibido no puede superar el stock teórico ni ser negativo
        $stockTeoricoPorProducto = [];
        foreach ($productosIds as $productId) {
            $stockTeorico = (float) (DB::table('stock_vendedor')
                              ->where('vendedor_id', $vendedor->id)
                              ->where('producto_id', $productId)
                              ->where('fecha_carga', $fecha)
                              ->where('estado', 1)
                              ->sum('cantidad_disponible') ?? 0);
            $stockFisico = (float) ($fisicoRecibido[$productId] ?? 0);

            if ($stockFisico < 0 || $stockFisico > $stockTeorico) {
                $producto = Productos::find($productId);
                $nombreProducto = $producto ? $producto->nomb_pro : "producto #{$productId}";
                return redirect()->back()->with('error', 'El físico recibido de "' . $nombreProducto . '" no puede ser mayor al stock teórico ni negativo.');
            }

            $stockTeoricoPorProducto[$productId] = $stockTeorico;
        }

        DB::beginTransaction();

        try {
            $idsede = session('key')->sede_id;
            $user_id = Auth::user()->id;
            $servicios = new FuncionesController;
            $envio = $servicios->tipo_envio_sunat();

            // Buscar ubicación destino: ubicación "Stock" del primer almacén de la sede
            $almacenPrincipal = Almacen::where('sede_id', $idsede)->first();
            if (!$almacenPrincipal) {
                throw new \Exception("No se encontró el almacén principal para la sede.");
            }

            $ubicacionDestino = DB::table('stock_location')
                                  ->where('almacen_id', $almacenPrincipal->id)
                                  ->where('name', 'Stock')
                                  ->first();

            if (!$ubicacionDestino) {
                throw new \Exception("No se encontró la ubicación de stock principal 'Stock'.");
            }

            $destino_id = $ubicacionDestino->id;

            // 1. Crear documento de traslado para auditoría
            $traslado = new Traslado;
            $traslado->fecha = date('Y-m-d');
            $traslado->hora = date('H:i:s');
            $traslado->serie = 'RET';
            // Obtener siguiente correlativo
            $ultimoTraslado = Traslado::where('serie', 'RET')->orderBy('id', 'desc')->first();
            $traslado->correlativo = $ultimoTraslado ? ((int)$ultimoTraslado->correlativo + 1) : 1;
            $traslado->almacen_origen = DB::table('stock_location')->where('id', $origen_id)->value('almacen_id');
            $traslado->almacen_destino = DB::table('stock_location')->where('id', $destino_id)->value('almacen_id');
            $traslado->id_ubicacion_origen = $origen_id;
            $traslado->id_ubicacion_destino = $destino_id;
            $traslado->motivo = 'RETORNO DE STOCK DE FURGONETA';
            $traslado->estado = 1; // 1 = RECIBIDO
            $traslado->tipo_envio = $envio;
            $traslado->sede_id = $idsede;
            $traslado->user_id = $user_id;
            $traslado->user_recepcion = $user_id;
            $traslado->fecha_recibido = date('Y-m-d');
            $traslado->hora_recibido = date('H:i:s');
            $observacion = trim((string) $request->input('observacion', ''));
            $traslado->observacion = $observacion ?: null;
            $traslado->save();

            // 2. Procesar productos
            foreach ($productosIds as $productId) {
                // Stock teórico = suma de lo cargado en stock_vendedor en la fecha reconciliada
                // (ya validado y calculado antes de abrir la transacción, se reutiliza para no repetir la consulta)
                $stockTeorico = $stockTeoricoPorProducto[$productId];

                $stockFisico = (float) ($fisicoRecibido[$productId] ?? 0);
                $diferencia = $stockTeorico - $stockFisico; // Positivo es pérdida, negativo excedente

                // Crear detalle del traslado
                $detalle = new Detalle_traslado;
                $detalle->producto_id = $productId;
                $detalle->traslado_id = $traslado->id;
                $detalle->cantidad = $stockTeorico;
                $detalle->cantidad_recibido = $stockFisico;
                $detalle->diferencia = $diferencia;
                $detalle->estado = 1;
                $detalle->save();

                // Cerrar registros de stock_vendedor (estado=2 = reportado/retornado)
                DB::table('stock_vendedor')
                    ->where('vendedor_id', $vendedor->id)
                    ->where('producto_id', $productId)
                    ->where('fecha_carga', $fecha)
                    ->where('estado', 1)
                    ->update([
                        'cantidad_disponible' => 0,
                        'estado' => 2,
                        'fecha_reporte' => date('Y-m-d H:i:s'),
                        'user_reporte_id' => $user_id,
                    ]);

                // Kardex de salida por la merma (lo cargado que el vendedor NO devuelve)
                if ($diferencia > 0) {
                    $servicios->movimiento_kardex_producto($destino_id, $productId, $diferencia, 2, "MERMA EN RETORNO DE FURGONETA", "RET", $traslado->correlativo, 0.0, 9, date('Y-m-d'), date('Y-m-d'));
                }

                // Ingresar SOLO el stock físico recibido en el almacén central
                if ($stockFisico > 0) {
                    $servicios->aumentar_descontar_stock(1, $destino_id, $productId, $stockFisico, $envio);
                    $servicios->movimiento_kardex_producto($destino_id, $productId, $stockFisico, 1, "RETORNO DE FURGONETA", "RET", $traslado->correlativo, 0.0, 9, date('Y-m-d'), date('Y-m-d'));
                }
            }

            // 3. Marcar todos los traslados CAR de la fecha reconciliada como cerrados (para que no aparezcan en venta del vendedor)
            // estado = 2 = CERRADA POR RETORNO (distinto de 0 = ANULADA, que es una reversión real de stock)
            // traslados.cliente_id guarda clientes.id (no users.id), hay que resolverlo primero.
            $clienteVendedorId = Clientes::where('usuario', $vendedor->id)->value('id');
            DB::table('traslados')
                ->where('cliente_id', $clienteVendedorId)
                ->where('motivo', self::MOTIVO_CARGA_STOCK)
                ->where('fecha', $fecha)
                ->where('estado', 1)
                ->update(['estado' => 2]);

            DB::commit();
            return redirect()->route('admin.retorno', ['fecha' => $fecha])->with('success', 'El retorno de mercadería fue procesado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar retorno de stock', [
                'vendedor_id' => $vendedorId,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'No se pudo procesar el retorno de stock. Intente nuevamente o contacte a soporte.');
        }
    }

    // C. Asignación de Ruta
    public function asignarRutaIndex(Request $request)
    {
        $idsede = session('key')->sede_id;

        // Obtener usuarios directamente de la tabla users unidos con model_has_roles para rol vendedor/cobrador
        $rolesIds = $this->rolesVendedorCobradorIds();
        $usuariosVendedores = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->whereIn('model_has_roles.role_id', $rolesIds)
            ->where('users.sede_id', $idsede)
            ->where('users.estado', 1)
            ->select('users.*')
            ->distinct()
            ->get();

        // Asegurar que cada usuario tenga registro en vendedores
        foreach ($usuariosVendedores as $u) {
            $this->resolveVendedor($u);
        }

        // Obtener vendedores activos cuyo usuario_id esté en la lista de usuarios con rol 6
        $vendedores = Vendedor::where('estado', 1)
                              ->whereIn('usuario_id', collect($usuariosVendedores)->pluck('id'))
                              ->get();

        $sectores = Sector::with('zona')->where('estado', 'ACTIVO')->orderBy('zona_id')->get();
        $zonas = Zona::where('estado', 'ACTIVO')->orderBy('nomb_zona')->get();

        // Filtro de fecha del historial: por defecto, del último mes hasta hoy.
        // Los campos siguen siendo editables si se quiere ver programación a futuro.
        // La búsqueda y la paginación de lo que cae dentro de este rango las maneja
        // DataTables del lado del cliente (mismo componente usado en el resto del panel).
        $fechaDesde = $request->input('fecha_desde', date('Y-m-d', strtotime('-1 month')));
        $fechaHasta = $request->input('fecha_hasta', date('Y-m-d'));

        $historial = VendedorSector::with(['vendedor', 'sector.zona'])
            ->where('fecha', '>=', $fechaDesde)
            ->when($fechaHasta, function ($q) use ($fechaHasta) {
                $q->where('fecha', '<=', $fechaHasta);
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('vendedor_id')
            ->get()
            ->groupBy(function ($item) {
                return $item->fecha . '_' . $item->vendedor_id;
            });

        return view('ventas_moviles.asignar', compact('vendedores', 'sectores', 'zonas', 'historial', 'fechaDesde', 'fechaHasta'));
    }

    public function asignarRutaGuardar(Request $request)
    {
        $request->validate([
            'vendedor_id' => 'required|exists:users,id',
            'sectores_ids' => 'required|array|min:1',
            'sectores_ids.*' => 'exists:sectores,id,estado,ACTIVO',
            'fecha' => 'required|date',
            'tipo' => 'required|in:VENTA,COBRANZA,AMBOS'
        ]);

        // Nota: aquí "vendedor_id" es el users.id (así lo guarda vendedor_sector), no el id
        // de la tabla vendedores -no aplica el helper vendedorPerteneceASede(), que espera este último-.
        $idsede = session('key')->sede_id;
        $perteneceASede = \App\User::where('id', $request->vendedor_id)
            ->where('sede_id', $idsede)
            ->exists();

        if (!$perteneceASede) {
            abort(403, 'El vendedor no pertenece a su sede.');
        }

        $creados = 0;
        $omitidos = 0;

        foreach ($request->sectores_ids as $sectorId) {
            // Evitar asignación duplicada del mismo sector al mismo vendedor el mismo día
            $existe = VendedorSector::where('vendedor_id', $request->vendedor_id)
                                    ->where('sector_id', $sectorId)
                                    ->where('fecha', $request->fecha)
                                    ->exists();

            if ($existe) {
                $omitidos++;
                continue;
            }

            VendedorSector::create([
                'vendedor_id' => $request->vendedor_id,
                'sector_id' => $sectorId,
                'fecha' => $request->fecha,
                'tipo' => $request->tipo
            ]);
            $creados++;
        }

        if ($creados === 0) {
            $mensaje = 'Todos los sectores seleccionados ya estaban asignados a este vendedor para esa fecha.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $mensaje], 422);
            }
            return redirect()->back()->with('error', $mensaje);
        }

        $mensaje = "Se asignaron {$creados} sector(es).";
        if ($omitidos > 0) {
            $mensaje .= " {$omitidos} ya estaba(n) asignado(s) para esa fecha y se omitieron.";
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $mensaje, 'creados' => $creados, 'omitidos' => $omitidos]);
        }

        return redirect()->back()->with('success', $mensaje);
    }

    public function asignarRutaEliminar($id)
    {
        $asignacion = VendedorSector::findOrFail($id);

        // asignacion->vendedor_id es el users.id (así lo guarda vendedor_sector), no el id
        // de la tabla vendedores -no aplica el helper vendedorPerteneceASede(), que espera este último-.
        $idsede = session('key')->sede_id;
        $perteneceASede = \App\User::where('id', $asignacion->vendedor_id)
            ->where('sede_id', $idsede)
            ->exists();

        if (!$perteneceASede) {
            abort(403, 'No autorizado para eliminar esta asignación.');
        }

        $asignacion->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Asignación de ruta eliminada exitosamente.']);
        }

        return redirect()->back()->with('success', 'Asignación de ruta eliminada exitosamente.');
    }

    // C. Carga independiente de Stock a Furgonetas
    public function cargarStockIndex(Request $request)
    {
        $idsede = session('key')->sede_id;
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $almacenPrincipal = Almacen::where('sede_id', $idsede)->first();
        if (!$almacenPrincipal) {
            return redirect()->back()->with('error', 'No se encontró el almacén principal de la sede.');
        }

        $ubicacionOrigen = DB::table('stock_location')
                            ->where('almacen_id', $almacenPrincipal->id)
                            ->where('name', 'Stock')
                            ->first();

        if (!$ubicacionOrigen) {
            return redirect()->back()->with('error', 'No se encontró la ubicación de stock principal.');
        }

        $productos = DB::table('detalle_almacen_productos as dp')
            ->join('productos as p', 'dp.producto_id', '=', 'p.id')
            ->select('p.id', 'p.nomb_pro', 'dp.stock')
            ->where('dp.ubicacion_id', '=', $ubicacionOrigen->id)
            ->where('dp.tipo_envio', '=', $envio)
            ->where('p.estado', '=', '1')
            ->where('dp.stock', '>', 0)
            ->orderBy('p.nomb_pro', 'asc')
            ->get();

        // Obtener usuarios activos de esta sede con rol vendedor/cobrador
        $rolesIds = $this->rolesVendedorCobradorIds();
        $vendedores = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function($q) use ($rolesIds) {
                $q->whereIn('id', $rolesIds);
            })->orderBy('name', 'asc')->get();

        // Advertir si un vendedor arrastra stock de furgoneta sin retornar de un día anterior
        $usuarioIds = $vendedores->pluck('id')->all();
        $pendientesPorVendedor = DB::table('stock_vendedor')
            ->select('vendedor_id', DB::raw('MIN(fecha_carga) as desde'))
            ->whereIn('vendedor_id', $usuarioIds)
            ->where('estado', 1)
            ->where('fecha_carga', '<', date('Y-m-d'))
            ->groupBy('vendedor_id')
            ->get()
            ->keyBy('vendedor_id');

        foreach ($vendedores as $v) {
            $pendiente = $pendientesPorVendedor->get($v->id);
            $v->stock_pendiente_desde = $pendiente ? $pendiente->desde : null;
        }

        return view('ventas_moviles.cargar_stock', compact('vendedores', 'productos', 'ubicacionOrigen'));
    }

    public function cargarStockProcesar(Request $request)
    {
        $vendedorUserId = $request->vendedor_id; // users.id del vendedor/cobrador
        $productosIds = $request->productos; // array de producto_id
        $cantidades = $request->cantidades; // array de producto_id => cantidad

        if (!$vendedorUserId || empty($productosIds)) {
            return redirect()->back()->with('error', 'Debe seleccionar un vendedor y al menos un producto.');
        }

        // Buscar al usuario con rol de vendedor/cobrador en la tabla users
        $rolesIds = $this->rolesVendedorCobradorIds();
        $vendedor = \App\User::where('id', $vendedorUserId)
            ->where('estado', 1)
            ->whereHas('roles', function($q) use ($rolesIds) {
                $q->whereIn('id', $rolesIds);
            })->first();
        if (!$vendedor) {
            return redirect()->back()->with('error', 'El usuario seleccionado no es un vendedor activo válido.');
        }

        // Validar que el vendedor tenga rutas asignadas para la fecha actual
        $fechaHoy = date('Y-m-d');
        $tieneRutasHoy = VendedorSector::where('vendedor_id', $vendedor->id)
                                       ->where('fecha', $fechaHoy)
                                       ->exists();
        if (!$tieneRutasHoy) {
            return redirect()->back()->with('error', 'El vendedor "' . $vendedor->name . '" no tiene rutas asignadas para la fecha actual (' . $fechaHoy . '). Debe asignarle sus rutas antes de poder cargar stock.');
        }

        // Advertir (no bloquear) si el vendedor arrastra stock de furgoneta sin retornar
        // de un día anterior. El flujo normal ya lo confirma del lado del cliente (JS);
        // esto es una red de seguridad por si se llega aquí sin pasar por esa confirmación.
        $tienePendiente = DB::table('stock_vendedor')
            ->where('vendedor_id', $vendedorUserId)
            ->where('estado', 1)
            ->where('fecha_carga', '<', $fechaHoy)
            ->exists();

        if ($tienePendiente && !$request->boolean('confirmar_pendiente')) {
            return redirect()->back()->with('error', 'El vendedor "' . $vendedor->name . '" tiene stock pendiente de retornar de un día anterior. Vuelva a intentar la carga para confirmar.');
        }

        // Asegurar que existe un registro en clientes para este vendedor
        $cliente = Clientes::where('usuario', $vendedor->id)->first();
        if (!$cliente) {
            // Generar documento secuencial: tomar el max, sumar 1, empezando desde 00000100
            $maxDoc = Clientes::where('tipo_doc', 0)
                ->where('documento', 'like', '00000%')
                ->max('documento');
            $nuevoDoc = $maxDoc
                ? str_pad((int)$maxDoc + 1, 8, '0', STR_PAD_LEFT)
                : '00000100';

            $cliente = Clientes::create([
                'nomb_per'    => $vendedor->name,
                'documento'   => $nuevoDoc,
                'tipo_doc'    => 0,
                'estado_per'  => '1',
                'usuario'     => $vendedor->id,
            ]);
        }

        DB::beginTransaction();

        try {
            $idsede = session('key')->sede_id;
            $user_id = Auth::user()->id;
            $servicios = new FuncionesController;
            $envio = $servicios->tipo_envio_sunat();

            // Ubicación origen: principal Stock de la sede
            $almacenPrincipal = Almacen::where('sede_id', $idsede)->first();
            $ubicacionOrigen = DB::table('stock_location')
                                  ->where('almacen_id', $almacenPrincipal->id)
                                  ->where('name', 'Stock')
                                  ->first();

            if (!$ubicacionOrigen) {
                throw new \Exception("No se encontró la ubicación de stock principal.");
            }

            $origen_id = $ubicacionOrigen->id;

            // Buscar ubicación destino: 'moviles'
            $ubicacionDestino = DB::table('stock_location')
                                  ->where('almacen_id', $almacenPrincipal->id)
                                  ->where(DB::raw('LOWER(name)'), 'moviles')
                                  ->first();
            
            if (!$ubicacionDestino) {
                $ubicacionDestino = DB::table('stock_location')
                                      ->where(DB::raw('LOWER(name)'), 'moviles')
                                      ->first();
            }

            if (!$ubicacionDestino) {
                throw new \Exception("No se encontró la ubicación destino 'moviles'. Por favor cree una ubicación de stock con nombre 'moviles'.");
            }
            
            $destino_id = $ubicacionDestino->id;

            // 1. Verificar si ya existe un traslado CAR activo para este vendedor hoy (estado 1 = CARGADO)
            // Si existe, se reutiliza para agregarle más productos; si no, se crea uno nuevo
            $trasladoExistente = Traslado::where('cliente_id', $cliente->id)
                ->where('fecha', $fechaHoy)
                ->where('estado', 1)  // CARGADO - buscar uno activo del día
                ->where('sede_id', $idsede)
                ->where('motivo', self::MOTIVO_CARGA_STOCK)
                ->first();

            if ($trasladoExistente) {
                // Reutilizar traslado existente (mantiene estado = 1 Cargado)
                $traslado = $trasladoExistente;
            } else {
                // Obtener correlativo de la tabla correlativos según GUIA INTERNA (tipo_comprobante_id = 7)
                $correlativoRecord = DB::table('correlativos')
                    ->where('sede_id', $idsede)
                    ->where('tipo_envio', $envio)
                    ->where('tipo_comprobante_id', 7) // GUIA INTERNA
                    ->first();

                if (!$correlativoRecord) {
                    throw new \Exception("No existe correlativo configurado para GUIA INTERNA en esta sede. Debe configurar el correlativo primero.");
                }

                // Usar la serie del correlativo (ej: CAR1, CAR, etc según lo configurado)
                $serieTraslado = $correlativoRecord->serie;
                $nuevoCorrelativo = $correlativoRecord->correlativo + 1;

                // Actualizar correlativo
                DB::table('correlativos')
                    ->where('id', $correlativoRecord->id)
                    ->update(['correlativo' => $nuevoCorrelativo]);

                // Crear nuevo documento de traslado
                $traslado = new Traslado;
                $traslado->fecha = date('Y-m-d');
                $traslado->hora = date('H:i:s');
                $traslado->serie = $serieTraslado;
                $traslado->correlativo = $nuevoCorrelativo;
                $traslado->almacen_origen = DB::table('stock_location')->where('id', $origen_id)->value('almacen_id');
                $traslado->almacen_destino = DB::table('stock_location')->where('id', $destino_id)->value('almacen_id');
                $traslado->id_ubicacion_origen = $origen_id;
                $traslado->id_ubicacion_destino = $destino_id;
                $traslado->motivo = 'CARGA DIARIA DE STOCK A MOVILES';
                $traslado->cliente_id = $cliente->id; // clientes.id del vendedor
                $traslado->estado = 1; // 1 = CARGADO (activa; 0 = ANULADA, 2 = CERRADA por retorno)
                $traslado->tipo_envio = $envio;
                $traslado->sede_id = $idsede;
                $traslado->user_id = $user_id;
                $traslado->tipo_traslado_id = 7; // GUIA INTERNA
                $traslado->id_documento_electronico = 7;
                $traslado->save();
            }

            // 2. Procesar productos
            foreach ($productosIds as $productId) {
                $cantidad = isset($cantidades[$productId]) ? (int)$cantidades[$productId] : 0;
                if ($cantidad <= 0) continue;

                // Validar stock disponible en el origen
                $stockOrigenRecord = Detalle_almacen_productos::where('ubicacion_id', $origen_id)
                                                              ->where('producto_id', $productId)
                                                              ->where('tipo_envio', $envio)
                                                              ->first();

                if (!$stockOrigenRecord || $stockOrigenRecord->stock < $cantidad) {
                    $nomProd = DB::table('productos')->where('id', $productId)->value('nomb_pro');
                    throw new \Exception("Stock insuficiente para el producto: " . $nomProd . ". Disponible: " . ($stockOrigenRecord->stock ?? 0));
                }

                // Descontar del origen (Almacén Principal)
                $servicios->aumentar_descontar_stock(0, $origen_id, $productId, $cantidad, $envio);

                // Registrar Kardex Salida (Origen)
                $precio_unitario = DB::table('precios')->where('articulo_id', $productId)->value('precio_contado') ?? 0;
                $descripSalida = 'CARGA DIARIA A MOVILES (Vendedor: ' . $vendedor->name . ')';
                $servicios->movimiento_kardex_producto($origen_id, $productId, $cantidad, 2, $descripSalida, $traslado->serie, $traslado->correlativo, $precio_unitario, 9, date('Y-m-d'), date('Y-m-d'));

                // Verificar si el producto ya existe en el detalle_traslado de este traslado
                $detalleExistente = Detalle_traslado::where('traslado_id', $traslado->id)
                    ->where('producto_id', $productId)
                    ->where('estado', 1)
                    ->first();

                if ($detalleExistente) {
                    // Sumar a la cantidad existente
                    $detalleExistente->cantidad += $cantidad;
                    $detalleExistente->save();
                } else {
                    // Crear nuevo detalle del traslado
                    $detalle = new Detalle_traslado;
                    $detalle->producto_id = $productId;
                    $detalle->traslado_id = $traslado->id;
                    $detalle->cantidad = $cantidad;
                    $detalle->estado = 1;
                    $detalle->save();
                }

                // Registrar en stock_vendedor (NO en detalle_almacen_productos de moviles)
                // Verificar si ya existe un registro para este producto en stock_vendedor del traslado actual
                $stockVendedorExistente = StockVendedor::where('vendedor_id', $vendedor->id)
                    ->where('producto_id', $productId)
                    ->where('traslado_id', $traslado->id)
                    ->where('estado', 1)
                    ->first();

                if ($stockVendedorExistente) {
                    // Actualizar cantidades
                    $stockVendedorExistente->cantidad_cargada += $cantidad;
                    $stockVendedorExistente->cantidad_disponible += $cantidad;
                    $stockVendedorExistente->save();
                } else {
                    // Crear nuevo registro en stock_vendedor
                    StockVendedor::create([
                        'vendedor_id' => $vendedor->id,
                        'producto_id' => $productId,
                        'traslado_id' => $traslado->id,
                        'detalle_traslado_id' => $detalle->id,
                        'cantidad_cargada' => $cantidad,
                        'cantidad_vendida' => 0,
                        'cantidad_disponible' => $cantidad,
                        'sede_id' => $idsede,
                        'tipo_envio' => $envio,
                        'fecha_carga' => $fechaHoy,
                        'estado' => 1,
                    ]);
                }

                // Registrar Kardex Entrada (Destino - ubicación móviles)
                $descripEntrada = 'CARGA DIARIA RECIBIDA EN MOVILES (Vendedor: ' . $vendedor->name . ')';
                $servicios->movimiento_kardex_producto($destino_id, $productId, $cantidad, 1, $descripEntrada, $traslado->serie, $traslado->correlativo, $precio_unitario, 9, date('Y-m-d'), date('Y-m-d'));
            }

            DB::commit();
            return redirect()->back()->with('success', 'El stock fue cargado exitosamente a la furgoneta de ' . $vendedor->name . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cargar stock: ' . $e->getMessage());
        }
    }

    // D. Historial de Carga Diaria de Stock a Furgonetas
    public function cargarStockHistorial(Request $request)
    {
        $idsede = session('key')->sede_id;
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $query = DB::table('traslados as t')
            ->leftJoin('clientes as cl', 'cl.id', '=', 't.cliente_id')
            ->leftJoin('vendedores as v', function ($join) {
                $join->on('v.usuario_id', '=', DB::raw('CASE WHEN cl.usuario ~ \'^[0-9]+$\' THEN cl.usuario::integer ELSE NULL END'));
            })
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->select(
                't.id',
                't.fecha',
                't.hora',
                't.serie',
                't.correlativo',
                't.motivo',
                't.estado',
                'v.nombre as vendedor_nombre',
                'u.name as usuario_nombre'
            )
            ->where('t.sede_id', $idsede)
            ->where('t.tipo_envio', $envio)
            ->where('t.motivo', self::MOTIVO_CARGA_STOCK);

        // Filtro por vendedor (vendedor_id llega como users.id, igual que cl.usuario)
        if ($request->filled('vendedor_id')) {
            $query->where('cl.usuario', $request->vendedor_id);
        }

        // Filtro por fecha (por defecto: hoy, para que coincida con los inputs)
        $fecha_desde = $request->filled('fecha_desde') ? $request->fecha_desde : date('Y-m-d');
        $fecha_hasta = $request->filled('fecha_hasta') ? $request->fecha_hasta : date('Y-m-d');
        $query->where('t.fecha', '>=', $fecha_desde)
              ->where('t.fecha', '<=', $fecha_hasta);

        $cargas = $query->orderBy('t.id', 'desc')->get();

        // Lista de vendedores activos de la sede para el filtro
        $rolesIds = $this->rolesVendedorCobradorIds();
        $usuariosVendedoresIds = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function($q) use ($rolesIds) { $q->whereIn('id', $rolesIds); })
            ->pluck('id');

        $vendedores = Vendedor::whereIn('usuario_id', $usuariosVendedoresIds)
            ->where('estado', 1)
            ->get();

        return view('ventas_moviles.historial_cargas', compact('cargas', 'vendedores'));
    }

    public function cargarStockDetalle($id)
    {
        $usuario = Auth::user();

        $traslado = DB::table('traslados as t')
            ->leftJoin('clientes as cl', 'cl.id', '=', 't.cliente_id')
            ->leftJoin('vendedores as v', function ($join) {
                $join->on('v.usuario_id', '=', DB::raw('CASE WHEN cl.usuario ~ \'^[0-9]+$\' THEN cl.usuario::integer ELSE NULL END'));
            })
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->select(
                't.id', 't.fecha', 't.hora', 't.serie', 't.correlativo',
                't.sede_id', 't.cliente_id',
                'cl.usuario as vendedor_usuario_id',
                'v.nombre as vendedor_nombre',
                'u.name as usuario_nombre'
            )
            ->where('t.id', $id)
            ->first();

        // La sede activa puede ser distinta de Auth::user()->sede_id si un super-admin
        // cambió de sede con el selector (session('key') queda con la sede elegida).
        $idsedeActiva = session('key')->sede_id;
        if (!$traslado || (int) $traslado->sede_id !== (int) $idsedeActiva) {
            return response()->json(['error' => 'Registro no encontrado.'], 404);
        }

        // Un vendedor/cobrador solo puede ver el detalle de sus propias cargas;
        // el panel admin (no vendedor/cobrador) puede ver cualquier carga de su sede.
        if ($usuario->esVendedorOCobrador() && (int) $traslado->vendedor_usuario_id !== (int) $usuario->id) {
            return response()->json(['error' => 'Registro no encontrado.'], 404);
        }

        $productos = DB::table('detalle_traslado as dt')
            ->join('productos as p', 'p.id', '=', 'dt.producto_id')
            ->leftJoin('precios as pr', 'pr.articulo_id', '=', 'p.id')
            ->select(
                'p.id',
                'p.nomb_pro',
                'dt.cantidad',
                DB::raw('COALESCE(pr.precio_contado, 0) as precio_unitario'),
                DB::raw('dt.cantidad * COALESCE(pr.precio_contado, 0) as subtotal')
            )
            ->where('dt.traslado_id', $id)
            ->get();

        return response()->json([
            'traslado' => $traslado,
            'productos' => $productos,
            'total_unidades' => $productos->sum('cantidad'),
            'total_valor'    => $productos->sum('subtotal'),
        ]);
    }

    // Productos disponibles en el almacén Stock (para agregar a una carga)
    public function productosStockAlmacen()
    {
        $idsede = session('key')->sede_id;
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $almacenPrincipal = Almacen::where('sede_id', $idsede)->first();
        if (!$almacenPrincipal) {
            return response()->json([]);
        }
        $ubicacionStock = DB::table('stock_location')
            ->where('almacen_id', $almacenPrincipal->id)
            ->where('name', 'Stock')
            ->first();

        if (!$ubicacionStock) {
            return response()->json([]);
        }

        $productos = DB::table('detalle_almacen_productos as dp')
            ->join('productos as p', 'dp.producto_id', '=', 'p.id')
            ->leftJoin('precios as pr', 'pr.articulo_id', '=', 'p.id')
            ->select(
                'p.id',
                'p.nomb_pro',
                'dp.stock',
                DB::raw('COALESCE(pr.precio_contado, 0) as precio_contado')
            )
            ->where('dp.ubicacion_id', '=', $ubicacionStock->id)
            ->where('dp.tipo_envio', '=', $envio)
            ->where('p.estado', '=', '1')
            ->where('dp.stock', '>', 0)
            ->orderBy('p.nomb_pro', 'asc')
            ->get();

        return response()->json($productos);
    }

    // Agregar productos a una carga existente
    public function agregarProductosCarga(Request $request)
    {
        $trasladoId = $request->traslado_id;
        $productosData = json_decode($request->productos, true);

        if (!$trasladoId || empty($productosData)) {
            return response()->json(['error' => 'Datos incompletos'], 400);
        }

        $traslado = Traslado::find($trasladoId);
        if (!$traslado || $traslado->motivo !== self::MOTIVO_CARGA_STOCK) {
            return response()->json(['error' => 'Traslado no encontrado'], 404);
        }

        $idsede = session('key')->sede_id;
        if ((int) $traslado->sede_id !== (int) $idsede) {
            return response()->json(['error' => 'No autorizado para modificar esta carga.'], 403);
        }

        if ($traslado->estado != 1) {
            return response()->json(['error' => 'No se puede modificar una carga anulada'], 400);
        }

        $user_id = Auth::user()->id;
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        // Obtener ubicación Stock
        $almacenPrincipal = Almacen::where('sede_id', $idsede)->first();
        if (!$almacenPrincipal) {
            return response()->json(['error' => 'No se encontró el almacén principal de la sede.'], 422);
        }
        $ubicacionStock = DB::table('stock_location')
            ->where('almacen_id', $almacenPrincipal->id)
            ->where('name', 'Stock')
            ->first();

        if (!$ubicacionStock) {
            return response()->json(['error' => 'No se encontró la ubicación Stock'], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($productosData as $item) {
                $productoId = $item['id'];
                $cantidad = (int) $item['cantidad'];

                if ($cantidad <= 0) continue;

                // Validar stock disponible en Stock
                $stockOrigen = Detalle_almacen_productos::where('ubicacion_id', $ubicacionStock->id)
                    ->where('producto_id', $productoId)
                    ->where('tipo_envio', $envio)
                    ->first();

                if (!$stockOrigen || $stockOrigen->stock < $cantidad) {
                    $nomProd = DB::table('productos')->where('id', $productoId)->value('nomb_pro');
                    throw new \RuntimeException("Stock insuficiente para el producto: $nomProd. Disponible: " . ($stockOrigen->stock ?? 0));
                }

                // Descontar del almacén Stock
                $servicios->aumentar_descontar_stock(0, $ubicacionStock->id, $productoId, $cantidad, $envio);

                // Generar Kardex de salida
                $precioUnitario = DB::table('precios')->where('articulo_id', $productoId)->value('precio_contado') ?? 0;
                $descrip = 'CARGA ADICIONAL A MOVILES (Traslado: ' . $traslado->serie . '-' . str_pad($traslado->correlativo, 4, '0', STR_PAD_LEFT) . ')';
                $servicios->movimiento_kardex_producto($ubicacionStock->id, $productoId, $cantidad, 2, $descrip, $traslado->serie, $traslado->correlativo, $precioUnitario, 9, date('Y-m-d'), date('Y-m-d'));

                // Agregar a detalle_traslado
                $detalleExistente = Detalle_traslado::where('traslado_id', $trasladoId)
                    ->where('producto_id', $productoId)
                    ->where('estado', 1)
                    ->first();

                if ($detalleExistente) {
                    $detalleExistente->cantidad += $cantidad;
                    $detalleExistente->save();
                    $detalleTrasladoId = $detalleExistente->id;
                } else {
                    $detalle = new Detalle_traslado;
                    $detalle->producto_id = $productoId;
                    $detalle->traslado_id = $trasladoId;
                    $detalle->cantidad = $cantidad;
                    $detalle->estado = 1;
                    $detalle->save();
                    $detalleTrasladoId = $detalle->id;
                }

                // Agregar a stock_vendedor
                $stockVendedorExistente = StockVendedor::where('vendedor_id', $traslado->cliente_id)
                    ->where('producto_id', $productoId)
                    ->where('traslado_id', $trasladoId)
                    ->where('estado', 1)
                    ->first();

                if ($stockVendedorExistente) {
                    $stockVendedorExistente->cantidad_cargada += $cantidad;
                    $stockVendedorExistente->cantidad_disponible += $cantidad;
                    $stockVendedorExistente->save();
                } else {
                    StockVendedor::create([
                        'vendedor_id' => $traslado->cliente_id,
                        'producto_id' => $productoId,
                        'traslado_id' => $trasladoId,
                        'detalle_traslado_id' => $detalleTrasladoId ?? null,
                        'cantidad_cargada' => $cantidad,
                        'cantidad_vendida' => 0,
                        'cantidad_disponible' => $cantidad,
                        'sede_id' => $idsede,
                        'tipo_envio' => $envio,
                        'fecha_carga' => date('Y-m-d'),
                        'estado' => 1,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => 'Productos agregados correctamente a la carga']);
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar productos a carga', [
                'traslado_id' => $trasladoId,
                'message' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'No se pudieron agregar los productos. Intente nuevamente o contacte a soporte.'], 500);
        }
    }

    public function vendedorHistorialCargas(Request $request)
    {
        $usuario = Auth::user();
        $vendedor = $this->resolveVendedor($usuario);
        
        $idsede = $usuario->sede_id;
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $query = DB::table('traslados as t')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->select(
                't.id',
                't.fecha',
                't.hora',
                't.serie',
                't.correlativo',
                't.motivo',
                't.estado',
                'u.name as usuario_nombre'
            )
            ->where('t.motivo', self::MOTIVO_CARGA_STOCK)
            ->where('t.cliente_id', $usuario->id)
            ->where('t.sede_id', $idsede)
            ->where('t.tipo_envio', $envio);

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->where('t.fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('t.fecha', '<=', $request->fecha_hasta);
        }

        $cargas = $query->orderBy('t.id', 'desc')->get();

        return view('ventas_moviles.vendedor_historial_cargas', compact('cargas', 'vendedor'));
    }

    public function vendedorHistorialVentas(Request $request)
    {
        $usuario = Auth::user();
        $vendedor = $this->resolveVendedor($usuario);
        
        $idsede = $usuario->sede_id;
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $query = DB::table('ventas as v')
            ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            ->leftJoin('tipo_comprobantes as tc', 'v.tipo_comprobante_id', '=', 'tc.id')
            ->leftJoin('tipo_pagos as tp', 'v.tipo_pago_id', '=', 'tp.id')
            ->select(
                'v.id',
                'v.fecha',
                'v.hora',
                'v.serie_comprobante',
                'v.numero_comprobante',
                'v.monto',
                'v.tipo_pago_id',
                'v.venta_estado',
                'v.estado_liquidacion',
                'v.fecha_eliminacion',
                'v.estado_nota',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as nombre_cliente"),
                'c.documento as documento_cliente',
                'tc.descripcion as tipo_comprobante',
                'tp.descripcion as tipo_pago'
            )
            ->where('v.vendedor_id', $vendedor->id)
            ->where('v.sede_id', $idsede)
            ->where('v.tipo_envio', $envio);

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->where('v.fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('v.fecha', '<=', $request->fecha_hasta);
        }

        // Filtro por búsqueda de texto
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('c.razon_social', 'ilike', "%$buscar%")
                    ->orWhere('c.nomb_per', 'ilike', "%$buscar%")
                    ->orWhere('c.pate_per', 'ilike', "%$buscar%")
                    ->orWhere('c.mate_per', 'ilike', "%$buscar%")
                    ->orWhere('c.documento', 'like', "%$buscar%")
                    ->orWhere('v.serie_comprobante', 'like', "%$buscar%")
                    ->orWhere('v.numero_comprobante', 'like', "%$buscar%");
            });
        }

        $ventas = $query->orderBy('v.id', 'desc')->get();

        return view('ventas_moviles.vendedor_historial_ventas', compact('ventas', 'vendedor'));
    }

    public function vendedorHistorialVentasDetalle($id)
    {
        $usuario = Auth::user();
        $vendedor = $this->resolveVendedor($usuario);
        $idsede = $usuario->sede_id;

        $venta = DB::table('ventas as v')
            ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            ->leftJoin('tipo_comprobantes as tc', 'v.tipo_comprobante_id', '=', 'tc.id')
            ->leftJoin('tipo_pagos as tp', 'v.tipo_pago_id', '=', 'tp.id')
            ->select(
                'v.id',
                'v.fecha',
                'v.hora',
                'v.serie_comprobante',
                'v.numero_comprobante',
                'v.monto',
                'v.descuento',
                'v.venta_estado',
                'v.estado_liquidacion',
                'v.fecha_eliminacion',
                'v.estado_nota',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as nombre_cliente"),
                'c.documento as documento_cliente',
                'tc.descripcion as tipo_comprobante',
                'tp.descripcion as tipo_pago'
            )
            ->where('v.id', $id)
            ->where('v.vendedor_id', $vendedor->id)
            ->where('v.sede_id', $idsede)
            ->first();

        if (!$venta) {
            return response()->json(['error' => 'Venta no encontrada.'], 404);
        }

        $productos = DB::table('detalle_venta as dv')
            ->join('productos as p', 'p.id', '=', 'dv.producto_id')
            ->select(
                'p.id',
                'p.nomb_pro',
                'dv.cantidad',
                'dv.precio',
                'dv.subtotal'
            )
            ->where('dv.venta_id', $id)
            ->get();

        return response()->json([
            'venta' => $venta,
            'productos' => $productos,
            'total_unidades' => $productos->sum('cantidad'),
            'total_monto' => $venta->monto
        ]);
    }

    public function vendedorHistorialCobros(Request $request)
    {
        $usuario = Auth::user();
        $vendedor = $this->resolveVendedor($usuario);
        
        $idsede = $usuario->sede_id;

        $query = DB::table('recibos as r')
            ->join('clientes as c', 'r.cliente_id', '=', 'c.id')
            ->leftJoin('movimientos as m', 'm.id', '=', 'r.id_movimiento')
            ->leftJoin('forma_pagos as fp', 'fp.id', '=', 'm.forma_pago_id')
            ->select(
                'r.id',
                'r.fech_rec',
                'r.num_recibo',
                'r.mont_rec',
                'r.esta_rec',
                'r.estado_liquidacion',
                'r.created_at',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as nombre_cliente"),
                'c.documento as documento_cliente',
                'fp.descripcion as forma_pago'
            )
            ->where('r.vendedor_id', $vendedor->id)
            ->where('r.sede_id', $idsede);

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->where('r.fech_rec', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('r.fech_rec', '<=', $request->fecha_hasta);
        }

        // Filtro por búsqueda de texto
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('c.razon_social', 'ilike', "%$buscar%")
                    ->orWhere('c.nomb_per', 'ilike', "%$buscar%")
                    ->orWhere('c.pate_per', 'ilike', "%$buscar%")
                    ->orWhere('c.mate_per', 'ilike', "%$buscar%")
                    ->orWhere('c.documento', 'like', "%$buscar%")
                    ->orWhere('r.num_recibo', 'like', "%$buscar%");
            });
        }

        $cobros = $query->orderBy('r.id', 'desc')->get();

        return view('ventas_moviles.vendedor_historial_cobros', compact('cobros', 'vendedor'));
    }

    public function vendedorHistorialCobrosDetalle($id)
    {
        $usuario = Auth::user();
        $vendedor = $this->resolveVendedor($usuario);
        $idsede = $usuario->sede_id;

        $recibo = DB::table('recibos as r')
            ->join('clientes as c', 'r.cliente_id', '=', 'c.id')
            ->leftJoin('movimientos as m', 'm.id', '=', 'r.id_movimiento')
            ->leftJoin('forma_pagos as fp', 'fp.id', '=', 'm.forma_pago_id')
            ->select(
                'r.id',
                'r.fech_rec',
                'r.num_recibo',
                'r.mont_rec',
                'r.esta_rec',
                'r.estado_liquidacion',
                'r.obse_rec',
                'r.created_at',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as nombre_cliente"),
                'c.documento as documento_cliente',
                'fp.descripcion as forma_pago'
            )
            ->where('r.id', $id)
            ->where('r.vendedor_id', $vendedor->id)
            ->where('r.sede_id', $idsede)
            ->first();

        if (!$recibo) {
            return response()->json(['error' => 'Recibo no encontrado.'], 404);
        }

        $amortizaciones = DB::table('amortizaciones as a')
            ->join('cuotas as cu', 'a.cuota_id', '=', 'cu.id')
            ->join('creditos as cr', 'cu.credito_id', '=', 'cr.id')
            ->leftJoin('ventas as v', 'cr.id_venta', '=', 'v.id')
            ->leftJoin('tipo_comprobantes as tc', 'v.tipo_comprobante_id', '=', 'tc.id')
            ->select(
                'a.id',
                'a.mont_amo',
                'a.capi_amo',
                'a.inte_amo',
                'a.saldo_cuo as saldo_restante_cuota',
                'cu.numero_cuo',
                'cu.mont_cuo',
                'cr.id as credito_id',
                'cr.impo_cre as total_credito',
                'v.serie_comprobante',
                'v.numero_comprobante',
                'tc.descripcion as tipo_comprobante'
            )
            ->where('a.recibo_id', $id)
            ->get();

        return response()->json([
            'recibo' => $recibo,
            'amortizaciones' => $amortizaciones,
            'total_amortizado' => $recibo->mont_rec
        ]);
    }

    // -----------------------------------------------------------------------
    // ANULAR CARGA DE STOCK (revierte movimientos de stock y kardex)
    // -----------------------------------------------------------------------
    public function anularCargaStock($id)
    {
        $traslado = Traslado::find($id);

        if (!$traslado || $traslado->motivo !== self::MOTIVO_CARGA_STOCK) {
            return response()->json(['error' => 'Registro de carga no encontrado.'], 404);
        }

        $idsede = session('key')->sede_id;
        if ((int) $traslado->sede_id !== (int) $idsede) {
            return response()->json(['error' => 'No autorizado para anular esta carga.'], 403);
        }

        if ($traslado->estado == 0) {
            return response()->json(['error' => 'Esta carga ya fue anulada previamente.'], 422);
        }

        $servicios  = new FuncionesController;
        $envio      = $servicios->tipo_envio_sunat();
        $user_id    = Auth::user()->id;

        $origen_id  = $traslado->id_ubicacion_origen;
        $destino_id = $traslado->id_ubicacion_destino;

        if (!$origen_id || !$destino_id) {
            return response()->json(['error' => 'No se pudieron determinar las ubicaciones de origen/destino de esta carga.'], 422);
        }

        DB::beginTransaction();
        try {
            // Obtener nombre del vendedor para el kardex
            $vendedor = Vendedor::where('usuario_id', $traslado->cliente_id)->first();
            $vendedorNombre = $vendedor ? $vendedor->nombre : 'Desconocido';

            $codCarga = $traslado->serie . '-' . str_pad($traslado->correlativo, 4, '0', STR_PAD_LEFT);

            $detalles = Detalle_traslado::where('traslado_id', $id)->get();

            foreach ($detalles as $detalle) {
                $productId = $detalle->producto_id;
                $cantidad  = $detalle->cantidad;

                // Verificar stock disponible en furgoneta (destino) para poder revertir
                $stockDestino = Detalle_almacen_productos::where('ubicacion_id', $destino_id)
                                                         ->where('producto_id', $productId)
                                                         ->where('tipo_envio', $envio)
                                                         ->first();

                if (!$stockDestino || $stockDestino->stock < $cantidad) {
                    $nomProd = DB::table('productos')->where('id', $productId)->value('nomb_pro');
                    $stockActual = $stockDestino ? $stockDestino->stock : 0;
                    throw new \RuntimeException(
                        "No se puede anular: el producto \"{$nomProd}\" tiene solo {$stockActual} unidad(es) en la furgoneta, " .
                        "pero se necesita revertir {$cantidad}. Es posible que parte del stock ya fue vendido."
                    );
                }

                // Revertir stock: descontar de furgoneta (destino)
                $servicios->aumentar_descontar_stock(0, $destino_id, $productId, $cantidad, $envio);

                // Revertir stock: aumentar en almacén principal (origen)
                $servicios->aumentar_descontar_stock(1, $origen_id, $productId, $cantidad, $envio);

                $precio_unitario = DB::table('precios')->where('articulo_id', $productId)->value('precio_contado') ?? 0;

                // Kardex: Entrada en origen (regresa al almacén)
                $servicios->movimiento_kardex_producto(
                    $origen_id, $productId, $cantidad, 1,
                    "ANULACIÓN CARGA {$codCarga} (Vendedor: {$vendedorNombre})",
                    $traslado->serie, $traslado->correlativo,
                    $precio_unitario, 9, date('Y-m-d'), date('Y-m-d')
                );

                // Kardex: Salida en destino (sale de la furgoneta)
                $servicios->movimiento_kardex_producto(
                    $destino_id, $productId, $cantidad, 2,
                    "ANULACIÓN CARGA {$codCarga} (Vendedor: {$vendedorNombre})",
                    $traslado->serie, $traslado->correlativo,
                    $precio_unitario, 9, date('Y-m-d'), date('Y-m-d')
                );
            }

            // Marcar traslado como anulado
            $traslado->estado = 0;
            $traslado->user_recepcion = $user_id;
            $traslado->save();

            DB::commit();

            return response()->json([
                'success' => "La carga {$codCarga} fue anulada exitosamente. El stock fue devuelto al almacén principal."
            ]);

        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al anular carga de stock', [
                'traslado_id' => $id,
                'message' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'No se pudo anular la carga. Intente nuevamente o contacte a soporte.'], 500);
        }
    }
}
