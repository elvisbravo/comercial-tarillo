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
        
        $vendedor = Vendedor::where('usuario_id', $usuario->id)
                            ->where('estado', 1)
                            ->first();

        if (!$vendedor) {
            return response("Error: Su usuario no está configurado como un vendedor activo. Por favor contacte al administrador.", 403);
        }

        // Sectores asignados para el día de hoy
        $fechaHoy = date('Y-m-d');
        $sectoresAsignados = VendedorSector::where('vendedor_id', $vendedor->id)
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
        $vendedor = Vendedor::where('usuario_id', $usuario->id)
                            ->where('estado', 1)
                            ->first();

        if (!$vendedor) {
            return redirect()->back()->with('error', 'Usuario no es vendedor.');
        }

        // Ubicación de la furgoneta
        $ubicacion_id = $vendedor->stock_location_id;
        if (!$ubicacion_id) {
            return redirect()->back()->with('error', 'Su usuario de vendedor no tiene una furgoneta (ubicación de stock) asignada.');
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
        $sectoresIds = VendedorSector::where('vendedor_id', $vendedor->id)
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
        $vendedor = Vendedor::where('usuario_id', $usuario->id)
                            ->where('estado', 1)
                            ->first();

        if (!$vendedor) {
            return redirect()->back()->with('error', 'Usuario no es vendedor.');
        }

        // Sectores del vendedor hoy
        $fechaHoy = date('Y-m-d');
        $sectoresIds = VendedorSector::where('vendedor_id', $vendedor->id)
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
        $vendedor = Vendedor::where('usuario_id', $usuario->id)
                            ->where('estado', 1)
                            ->first();

        if (!$vendedor) {
            return redirect()->back()->with('error', 'Usuario no es vendedor.');
        }

        $ubicacion_id = $vendedor->stock_location_id;
        if (!$ubicacion_id) {
            return redirect()->back()->with('error', 'No tiene furgoneta asignada.');
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

        // Obtener todos los vendedores activos
        $vendedores = Vendedor::where('estado', 1)->get();

        $totalGralEfectivo = 0;
        $totalGralVentasContado = 0;
        $totalGralVentasCredito = 0;
        $totalGralCobranzas = 0;

        foreach ($vendedores as $v) {
            // Ventas pendientes de liquidar
            $v_ventas = Venta::where('vendedor_id', $v->id)
                             ->where('estado_liquidacion', 'PENDIENTE')
                             ->get();
            
            $v->total_ventas_contado = $v_ventas->where('tipo_venta', 1)->sum('monto');
            $v->total_ventas_credito = $v_ventas->where('tipo_venta', 2)->sum('monto');
            
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

        if ($request->input('format') == 'json' && $request->vendedor_id) {
            $vendedor = Vendedor::find($request->vendedor_id);
            $stock = DB::table('detalle_almacen_productos as dp')
                ->join('productos as p', 'dp.producto_id', '=', 'p.id')
                ->select('p.id', 'p.nomb_pro', 'dp.stock')
                ->where('dp.ubicacion_id', '=', $vendedor->stock_location_id)
                ->where('dp.tipo_envio', '=', $envio)
                ->where('p.estado', '=', '1')
                ->where('dp.stock', '>', 0)
                ->get();
            return response()->json($stock);
        }

        $vendedores = Vendedor::with('stockLocation')
                              ->whereNotNull('stock_location_id')
                              ->where('estado', 1)
                              ->get();

        foreach ($vendedores as $v) {
            $v_stock = DB::table('detalle_almacen_productos as dp')
                ->join('productos as p', 'dp.producto_id', '=', 'p.id')
                ->where('dp.ubicacion_id', '=', $v->stock_location_id)
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
        $origen_id = $vendedor->stock_location_id;

        if (!$origen_id) {
            return redirect()->back()->with('error', 'El vendedor no tiene ubicación de stock.');
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
            $traslado->almacen_origen = 'FURGONETA: ' . $vendedor->nombre;
            $traslado->almacen_destino = 'ALMACEN PRINCIPAL';
            $traslado->id_ubicacion_origen = $origen_id;
            $traslado->id_ubicacion_destino = $destino_id;
            $traslado->motivo = 'RETORNO DE STOCK DE FURGONETA';
            $traslado->estado = 'RECIBIDO';
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
        $vendedores = Vendedor::where('estado', 1)->get();
        $sectores = Sector::where('estado', 'ACTIVO')->get();

        $historial = VendedorSector::with(['vendedor', 'sector'])
                                    ->orderBy('fecha', 'desc')
                                    ->orderBy('id', 'desc')
                                    ->get();

        return view('ventas_moviles.asignar', compact('vendedores', 'sectores', 'historial'));
    }

    public function asignarRutaGuardar(Request $request)
    {
        $request->validate([
            'vendedor_id' => 'required|exists:vendedores,id',
            'sector_id' => 'required|exists:sectores,id',
            'fecha' => 'required|date'
        ]);

        // Evitar asignación duplicada del mismo sector al mismo vendedor el mismo día
        $existe = VendedorSector::where('vendedor_id', $request->vendedor_id)
                                ->where('sector_id', $request->sector_id)
                                ->where('fecha', $request->fecha)
                                ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'Esta asignación de ruta ya existe para esta fecha.');
        }

        VendedorSector::create([
            'vendedor_id' => $request->vendedor_id,
            'sector_id' => $request->sector_id,
            'fecha' => $request->fecha
        ]);

        return redirect()->back()->with('success', 'Ruta asignada exitosamente.');
    }

    public function asignarRutaEliminar($id)
    {
        $asignacion = VendedorSector::findOrFail($id);
        $asignacion->delete();

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

        $vendedores = Vendedor::with('stockLocation')
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
        $destino_id = $vendedor->stock_location_id; // Furgoneta del vendedor

        if (!$destino_id) {
            return redirect()->back()->with('error', 'El vendedor no tiene ubicación de stock (furgoneta) asignada.');
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

            // 1. Crear documento de traslado para auditoría de la carga
            $traslado = new Traslado;
            $traslado->fecha = date('Y-m-d');
            $traslado->hora = date('H:i:s');
            $traslado->serie = 'CAR';
            $ultimoTraslado = Traslado::where('serie', 'CAR')->orderBy('id', 'desc')->first();
            $traslado->correlativo = $ultimoTraslado ? ((int)$ultimoTraslado->correlativo + 1) : 1;
            $traslado->almacen_origen = 'ALMACEN PRINCIPAL';
            $traslado->almacen_destino = 'FURGONETA: ' . $vendedor->nombre;
            $traslado->id_ubicacion_origen = $origen_id;
            $traslado->id_ubicacion_destino = $destino_id;
            $traslado->motivo = 'CARGA DIARIA DE STOCK A FURGONETA';
            $traslado->estado = 'RECIBIDO';
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

                // Aumentar en el destino (Furgoneta)
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
                    $stockDestinoRecord->almacen_id = DB::table('stock_location')->where('id', $destino_id)->value('almacen_id');
                    $stockDestinoRecord->save();
                }

                $servicios->aumentar_descontar_stock(1, $destino_id, $productId, $cantidad, $envio);

                // Registrar Kardex Salida (Origen)
                $precio_unitario = DB::table('precios')->where('articulo_id', $productId)->value('precio_contado') ?? 0;
                $descripSalida = 'CARGA DIARIA A FURGONETA ' . $vendedor->nombre;
                $servicios->movimiento_kardex_producto($origen_id, $productId, $cantidad, 2, $descripSalida, $traslado->serie, $traslado->correlativo, $precio_unitario, 'CARGA', date('Y-m-d'), date('Y-m-d'));

                // Registrar Kardex Entrada (Destino)
                $descripEntrada = 'CARGA DIARIA RECIBIDA EN FURGONETA';
                $servicios->movimiento_kardex_producto($destino_id, $productId, $cantidad, 1, $descripEntrada, $traslado->serie, $traslado->correlativo, $precio_unitario, 'CARGA', date('Y-m-d'), date('Y-m-d'));

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
