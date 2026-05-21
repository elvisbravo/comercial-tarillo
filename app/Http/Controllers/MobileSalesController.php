<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendedor;
use App\VendedorSector;
use App\Sector;
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
use App\Almacen;
use App\Tipo_comprobantes;
use App\Tipo_documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\servicios\FuncionesController;

class MobileSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if ($request->is('vendedor/*') || $request->is('vendedor')) {
                if ($user && !$user->roles()->where('id', 6)->exists()) {
                    abort(403, 'Acceso denegado. Este módulo es exclusivo para vendedores con perfil ID 6.');
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

        return view('ventas_moviles.dashboard', compact(
            'vendedor', 
            'sectoresAsignados', 
            'totalVentas', 
            'totalCobranzas', 
            'cantVentas', 
            'cantCobranzas'
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

        // Productos con stock en la furgoneta
        $productos = DB::table('productos as p')
            ->join('detalle_almacen_productos as dp', 'dp.producto_id', '=', 'p.id')
            ->leftJoin('precios as pr', 'p.id', '=', 'pr.articulo_id')
            ->select('p.id', 'p.nomb_pro', 'dp.stock', 'pr.precio_contado', 'pr.precio_credito')
            ->where('dp.ubicacion_id', '=', $ubicacion_id)
            ->where('dp.tipo_envio', '=', $envio)
            ->where('p.estado', '=', '1')
            ->where('dp.stock', '>', 0)
            ->get();

        // Clientes de los sectores asignados para hoy
        $fechaHoy = date('Y-m-d');
        $sectoresIds = VendedorSector::where('vendedor_id', $usuario->id)
                                    ->where('fecha', $fechaHoy)
                                    ->pluck('sector_id')
                                    ->toArray();

        $clientes = Clientes::where('estado_per', '=', '1')
                            ->whereIn('id_sector', $sectoresIds)
                            ->get();

        // Tipos de comprobante
        $comprobantes = Tipo_comprobantes::whereIn('id', [1, 2, 9])->get(); // Boleta, Factura, Nota de Venta

        // Documentos de identidad
        $tipo_documento = Tipo_documento::all();

        // Formas de pago y bancos
        $forma_pagos = DB::table('forma_pagos')->orderBy('id', 'asc')->get();
        $bancos = DB::table('cuentas_bancarias as cb')
            ->join('bancos as b', 'b.id', '=', 'cb.banco_id')
            ->select('cb.id', 'cb.cuenta_corriente', 'b.abreviatura')
            ->get();
        
        $sectores = Sector::where('estado', 'ACTIVO')->get();

        return view('ventas_moviles.venta', compact(
            'vendedor', 
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

    // A. Liquidación de caja en efectivo
    public function liquidarCajaIndex(Request $request)
    {
        if ($request->input('format') == 'json' && $request->vendedor_id) {
            $vendedorId = $request->vendedor_id;
            $ventas = Venta::where('vendedor_id', $vendedorId)
                            ->where('estado_liquidacion', 'PENDIENTE')
                            ->get();

            $cobros = Recibos::with('cliente')->where('vendedor_id', $vendedorId)
                             ->where('estado_liquidacion', 'PENDIENTE')
                             ->get();

            return response()->json([
                'ventas' => $ventas,
                'cobros' => $cobros
            ]);
        }

        // Obtener sede activa de la sesión
        $idsede = session('key')->sede_id;

        // Obtener usuarios activos de esta sede con rol ID 6
        $usuariosVendedoresIds = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function($q) {
                $q->where('id', 6);
            })->pluck('id');

        $vendedores = Vendedor::where('estado', 1)
                              ->whereIn('usuario_id', $usuariosVendedoresIds)
                              ->get();

        $totalGralEfectivo = 0;
        $totalGralVentasContado = 0;
        $totalGralVentasCredito = 0;
        $totalGralCobranzas = 0;

        foreach ($vendedores as $v) {
            // Ventas pendientes de liquidar
            $v_ventas = Venta::where('vendedor_id', $v->id)
                             ->where('estado_liquidacion', 'PENDIENTE')
                             ->get();
            
            $v->total_ventas_contado = $v_ventas->where('tipo_pago_id', 1)->sum('monto');
            $v->total_ventas_credito = $v_ventas->where('tipo_pago_id', 2)->sum('monto');
            
            // Cobros de crédito pendientes de liquidar
            $v->total_cobros_credito = Recibos::where('vendedor_id', $v->id)
                                             ->where('estado_liquidacion', 'PENDIENTE')
                                             ->sum('mont_rec');
            
            $v->total_efectivo_pendiente = $v->total_ventas_contado + $v->total_cobros_credito;
            $v->tiene_pendiente = $v->total_efectivo_pendiente > 0 || $v->total_ventas_credito > 0;

            // Totales acumulados generales
            $totalGralEfectivo += $v->total_efectivo_pendiente;
            $totalGralVentasContado += $v->total_ventas_contado;
            $totalGralVentasCredito += $v->total_ventas_credito;
            $totalGralCobranzas += $v->total_cobros_credito;
        }

        return view('ventas_moviles.liquidar', compact(
            'vendedores',
            'totalGralEfectivo',
            'totalGralVentasContado',
            'totalGralVentasCredito',
            'totalGralCobranzas'
        ));
    }

    public function liquidarCajaProcesar(Request $request)
    {
        $vendedorId = $request->vendedor_id;
        if (!$vendedorId) {
            return redirect()->back()->with('error', 'Debe seleccionar un vendedor.');
        }

        DB::beginTransaction();

        try {
            $idsede = session('key')->sede_id;
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
                           ->get();

            foreach ($ventas as $venta) {
                $ventaFormapago = DB::table('venta_formapagos')->where('venta_id', $venta->id)->get();
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
            }

            // 2. Procesar Amortizaciones (Recibos)
            $recibos = Recibos::where('vendedor_id', $vendedorId)
                              ->where('estado_liquidacion', 'PENDIENTE')
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
            return redirect()->route('vendedor.dashboard')->with('success', 'La caja del vendedor fue liquidada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al liquidar caja: ' . $e->getMessage());
        }
    }

    // B. Reconciliación y Retorno de Stock
    public function retornoStockIndex(Request $request)
    {
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $idsede = session('key')->sede_id;
        $almacenPrincipal = \App\Almacen::where('sede_id', $idsede)->first();
        $ubicacionMoviles = DB::table('stock_location')
                                ->where('almacen_id', $almacenPrincipal->id)
                                ->where(DB::raw('LOWER(name)'), 'moviles')
                                ->first();
        
        $moviles_id = $ubicacionMoviles ? $ubicacionMoviles->id : null;

        if ($request->input('format') == 'json' && $request->vendedor_id) {
            $vendedor = Vendedor::find($request->vendedor_id);
            $stock = DB::table('detalle_almacen_productos as dp')
                ->join('productos as p', 'dp.producto_id', '=', 'p.id')
                ->select('p.id', 'p.nomb_pro', 'dp.stock')
                ->where('dp.ubicacion_id', '=', $moviles_id)
                ->where('dp.tipo_envio', '=', $envio)
                ->where('p.estado', '=', '1')
                ->where('dp.stock', '>', 0)
                ->get();
            return response()->json($stock);
        }

        // Obtener sede activa de la sesión
        $idsede = session('key')->sede_id;

        // Obtener usuarios activos de esta sede con rol ID 6
        $usuariosVendedoresIds = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function($q) {
                $q->where('id', 6);
            })->pluck('id');

        $vendedores = Vendedor::with('stockLocation')
                              ->whereIn('usuario_id', $usuariosVendedoresIds)
                              ->whereNotNull('stock_location_id')
                              ->where('estado', 1)
                              ->get();

        foreach ($vendedores as $v) {
            $v_stock = DB::table('detalle_almacen_productos as dp')
                ->join('productos as p', 'dp.producto_id', '=', 'p.id')
                ->where('dp.ubicacion_id', '=', $moviles_id)
                ->where('dp.tipo_envio', '=', $envio)
                ->where('p.estado', '=', '1')
                ->where('dp.stock', '>', 0)
                ->select('dp.stock')
                ->get();
            
            $v->total_items = $v_stock->count();
            $v->total_unidades = $v_stock->sum('stock');
        }

        return view('ventas_moviles.retorno', compact('vendedores'));
    }

    public function retornoStockProcesar(Request $request)
    {
        $vendedorId = $request->vendedor_id;
        $productosIds = $request->productos; // array
        $fisicoRecibido = $request->fisico; // array de producto_id => cantidad_fisica

        if (!$vendedorId || empty($productosIds)) {
            return redirect()->back()->with('error', 'Debe seleccionar un vendedor y al menos un producto.');
        }

        $vendedor = Vendedor::find($vendedorId);
        $idsede = session('key')->sede_id;
        
        $almacenPrincipal = \App\Almacen::where('sede_id', $idsede)->first();
        $ubicacionMoviles = DB::table('stock_location')
                                ->where('almacen_id', $almacenPrincipal->id)
                                ->where(DB::raw('LOWER(name)'), 'moviles')
                                ->first();
        
        $origen_id = $ubicacionMoviles ? $ubicacionMoviles->id : null;

        if (!$origen_id) {
            return redirect()->back()->with('error', 'No se encontró la ubicación de stock "moviles".');
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
            $traslado->save();

            // 2. Procesar productos
            foreach ($productosIds as $productId) {
                $stockTeorico = DB::table('detalle_almacen_productos')
                                  ->where('ubicacion_id', $origen_id)
                                  ->where('producto_id', $productId)
                                  ->where('tipo_envio', $envio)
                                  ->value('stock') ?? 0;

                $stockFisico = (int)($fisicoRecibido[$productId] ?? 0);
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

                // Vaciar furgoneta (restar TODO el stock teórico actual de la furgoneta)
                $servicios->aumentar_descontar_stock(0, $origen_id, $productId, $stockTeorico, $envio);
                $servicios->movimiento_kardex_producto($origen_id, $productId, $stockTeorico, 2, "RETORNO DE FURGONETA", "RET", $traslado->correlativo, 0.0, 9, date('Y-m-d'), date('Y-m-d'));

                // Ingresar SOLO el stock físico recibido en el almacén central
                if ($stockFisico > 0) {
                    $servicios->aumentar_descontar_stock(1, $destino_id, $productId, $stockFisico, $envio);
                    $servicios->movimiento_kardex_producto($destino_id, $productId, $stockFisico, 1, "RETORNO DE FURGONETA", "RET", $traslado->correlativo, 0.0, 9, date('Y-m-d'), date('Y-m-d'));
                }
            }

            DB::commit();
            return redirect()->route('vendedor.dashboard')->with('success', 'El retorno de mercadería fue procesado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar retorno: ' . $e->getMessage());
        }
    }

    // C. Asignación de Ruta
    public function asignarRutaIndex(Request $request)
    {
        // Obtener sede activa de la sesión
        $idsede = session('key')->sede_id;

        // Obtener usuarios activos de esta sede con rol ID 6 y asegurar que tengan registro de vendedor creado/activo
        $usuariosVendedores = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function($q) {
                $q->where('id', 6);
            })->get();

        foreach ($usuariosVendedores as $u) {
            $this->resolveVendedor($u);
        }

        $vendedores = Vendedor::where('estado', 1)
                              ->whereIn('usuario_id', $usuariosVendedores->pluck('id'))
                              ->get();
        $sectores = Sector::where('estado', 'ACTIVO')->get();

        $historial = VendedorSector::with(['vendedor', 'sector'])
                                    ->orderBy('fecha', 'desc')
                                    ->get()
                                    ->groupBy(function($item) {
                                        return $item->fecha . '_' . $item->vendedor_id;
                                    });

        return view('ventas_moviles.asignar', compact('vendedores', 'sectores', 'historial'));
    }

    public function asignarRutaGuardar(Request $request)
    {
        $request->validate([
            'vendedor_id' => 'required|exists:users,id',
            'sector_id' => 'required|exists:sectores,id',
            'fecha' => 'required|date'
        ]);

        // Evitar asignación duplicada del mismo sector al mismo vendedor el mismo día
        $existe = VendedorSector::where('vendedor_id', $request->vendedor_id)
                                ->where('sector_id', $request->sector_id)
                                ->where('fecha', $request->fecha)
                                ->exists();

        if ($existe) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Esta asignación de ruta ya existe para esta fecha.'], 422);
            }
            return redirect()->back()->with('error', 'Esta asignación de ruta ya existe para esta fecha.');
        }

        VendedorSector::create([
            'vendedor_id' => $request->vendedor_id,
            'sector_id' => $request->sector_id,
            'fecha' => $request->fecha
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Ruta asignada exitosamente.']);
        }

        return redirect()->back()->with('success', 'Ruta asignada exitosamente.');
    }

    public function asignarRutaEliminar($id)
    {
        $asignacion = VendedorSector::findOrFail($id);
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

        // Obtener usuarios activos de esta sede con rol ID 6
        $usuariosVendedoresIds = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function($q) {
                $q->where('id', 6);
            })->pluck('id');

        $vendedores = Vendedor::with('stockLocation')
                              ->whereIn('usuario_id', $usuariosVendedoresIds)
                              ->whereNotNull('stock_location_id')
                              ->where('estado', 1)
                              ->get();

        return view('ventas_moviles.cargar_stock', compact('vendedores', 'productos', 'ubicacionOrigen'));
    }

    public function cargarStockProcesar(Request $request)
    {
        $vendedorId = $request->vendedor_id;
        $productosIds = $request->productos; // array de producto_id
        $cantidades = $request->cantidades; // array de producto_id => cantidad

        if (!$vendedorId || empty($productosIds)) {
            return redirect()->back()->with('error', 'Debe seleccionar un vendedor y al menos un producto.');
        }

        $vendedor = Vendedor::find($vendedorId);
        
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

            // 1. Crear documento de traslado para auditoría de la carga
            $traslado = new Traslado;
            $traslado->fecha = date('Y-m-d');
            $traslado->hora = date('H:i:s');
            $traslado->serie = 'CAR';
            $ultimoTraslado = Traslado::where('serie', 'CAR')->orderBy('id', 'desc')->first();
            $traslado->correlativo = $ultimoTraslado ? ((int)$ultimoTraslado->correlativo + 1) : 1;
            $traslado->almacen_origen = DB::table('stock_location')->where('id', $origen_id)->value('almacen_id');
            $traslado->almacen_destino = DB::table('stock_location')->where('id', $destino_id)->value('almacen_id');
            $traslado->id_ubicacion_origen = $origen_id;
            $traslado->id_ubicacion_destino = $destino_id;
            $traslado->motivo = 'CARGA DIARIA DE STOCK A MOVILES';
            $traslado->estado = 1; // 1 = RECIBIDO
            $traslado->tipo_envio = $envio;
            $traslado->sede_id = $idsede;
            $traslado->user_id = $user_id;
            $traslado->user_recepcion = $user_id;
            $traslado->fecha_recibido = date('Y-m-d');
            $traslado->hora_recibido = date('H:i:s');
            $traslado->save();

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

                // Aumentar en el destino (Moviles)
                $stockDestinoRecord = Detalle_almacen_productos::where('ubicacion_id', $destino_id)
                                                               ->where('producto_id', $productId)
                                                               ->where('tipo_envio', $envio)
                                                               ->first();
                if (!$stockDestinoRecord) {
                    $stockDestinoRecord = new Detalle_almacen_productos;
                    $stockDestinoRecord->ubicacion_id = $destino_id;
                    $stockDestinoRecord->producto_id = $productId;
                    $stockDestinoRecord->tipo_envio = $envio;
                    $stockDestinoRecord->stock = 0;
                    $stockDestinoRecord->save();
                }

                $servicios->aumentar_descontar_stock(1, $destino_id, $productId, $cantidad, $envio);

                // Registrar Kardex Salida (Origen)
                $precio_unitario = DB::table('precios')->where('articulo_id', $productId)->value('precio_contado') ?? 0;
                $descripSalida = 'CARGA DIARIA A MOVILES (Vendedor: ' . $vendedor->nombre . ')';
                $servicios->movimiento_kardex_producto($origen_id, $productId, $cantidad, 2, $descripSalida, $traslado->serie, $traslado->correlativo, $precio_unitario, 9, date('Y-m-d'), date('Y-m-d'));

                // Registrar Kardex Entrada (Destino)
                $descripEntrada = 'CARGA DIARIA RECIBIDA EN MOVILES (Vendedor: ' . $vendedor->nombre . ')';
                $servicios->movimiento_kardex_producto($destino_id, $productId, $cantidad, 1, $descripEntrada, $traslado->serie, $traslado->correlativo, $precio_unitario, 9, date('Y-m-d'), date('Y-m-d'));

                // Crear detalle del traslado
                $detalle = new Detalle_traslado;
                $detalle->producto_id = $productId;
                $detalle->traslado_id = $traslado->id;
                $detalle->cantidad = $cantidad;
                $detalle->estado = 1;
                $detalle->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'El stock fue cargado exitosamente a la furgoneta de ' . $vendedor->nombre . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cargar stock: ' . $e->getMessage());
        }
    }
}
