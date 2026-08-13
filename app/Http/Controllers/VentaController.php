<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Productos;
use App\Tipo_documento;
use App\Tipo_comprobantes;
use App\Detalle_venta;
use App\Venta;
use App\Clientes;
use App\Creditos;
use App\Cuotas;
use App\Precios;
use App\Empresa;
use App\Correlativos;
use App\Almacen;
use App\Caja;
use App\candado;
use App\Categorias;
use App\Forma_pago;
use App\Venta_formapago;
use App\Vendedor;
use App\StockVendedor;
use App\Sector;
use App\Sede;
use Spatie\Permission\Models\Role;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\servicios\FuncionesController;
use App\Ubigeo;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

use NumberFormatter;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware('auth');
        //$this->middleware('permission:Modulo Ventas');
        //$this->middleware('Lista Precios');
        //$this->middleware('Precios');


    }


    public function index()
    {
        return view('ventas.index');
    }

    public function listado(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'];

        $idsede = session('key')->sede_id;
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $query = DB::table('ventas')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->join('tipo_comprobantes', 'ventas.tipo_comprobante_id', '=', 'tipo_comprobantes.id')
            ->select(
                'clientes.nomb_per',
                'clientes.pate_per',
                'clientes.mate_per',
                'clientes.documento',
                'tipo_comprobantes.descripcion as comprobante',
                'ventas.id',
                DB::raw("to_char(ventas.fecha, 'DD-MM-YYYY') as fecha_formateada"),
                'ventas.fecha',
                'ventas.hora',
                'ventas.serie_comprobante',
                'ventas.numero_comprobante',
                'ventas.monto',
                'ventas.sede_id',
                'ventas.venta_estado',
                'ventas.aceptado_sunat',
                'ventas.mensaje_sunat',
                'ventas.tipo_comprobante_id',
                'ventas.estado_nota',
                'ventas.serie_nota_credito',
                'ventas.numero_nota_credito',
                'ventas.tipo_pago_id'
            )
            ->where('ventas.tipo_envio', '=', $envio)
            ->where('ventas.sede_id', '=', $idsede);

        $totalRecords = $query->count();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('clientes.razon_social', 'ilike', "%$search%")
                    ->orWhere('clientes.documento', 'like', "%$search%")
                    ->orWhere('clientes.nomb_per', 'ilike', "%$search%")
                    ->orWhere('ventas.serie_comprobante', 'like', "%$search%")
                    ->orWhere('ventas.numero_comprobante', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(ventas.serie_comprobante, '-', ventas.numero_comprobante) ILIKE ?", ["%$search%"]);
            });
        }

        $filteredRecords = $query->count();

        // Ordenamiento
        $orderColumnIndex = $request->get('order')[0]['column'];
        $orderDir = $request->get('order')[0]['dir'];
        $columnsOrder = ['ventas.id', 'ventas.fecha', 'ventas.serie_comprobante', 'clientes.razon_social', 'ventas.monto', 'ventas.aceptado_sunat'];
        $orderColumn = $columnsOrder[$orderColumnIndex] ?? 'ventas.id';

        $data = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function pos()
    {
        $idsede = session('key')->sede_id;

        $almacenes = Almacen::where('sede_id', '=', $idsede)->get();

        $idFirst = $almacenes[0]['id'];

        $ubicaciones = DB::table('almacenes as a')
            ->join('stock_location as s', 's.almacen_id', '=', 'a.id')
            ->select('s.id', 's.name')
            ->where('s.almacen_id', '=', $idFirst)
            ->where('s.name', '!=', 'Transferencias')
            ->orderByRaw("CASE WHEN s.name = 'Stock' THEN 1 ELSE 2 END")
            ->orderBy('s.name', 'asc')
            ->get();

        $userId = session('key')->id;

        $vendedores = Vendedor::where('estado', '=', 1)->get();

        $sectores = Sector::with('zona')->where('estado', '=', 'ACTIVO')->orderBy('zona_id')->get();

        return view('ventas.pos', compact('almacenes', 'ubicaciones', 'userId', 'vendedores', 'sectores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $idsede = session('key')->sede_id;

        $caja = Caja::where('sede_id', '=', $idsede)->where('fecha_apertura', '=', date('Y-m-d'))->first();

        $tipo_documento = Tipo_documento::all();
        return view('ventas.create', compact('tipo_documento', 'caja'));
    }

    public function render_productos(Request $request)
    {
        //$idsede = session('key')->sede_id;

        //$almacen = Almacen::where('sede_id', '=', $idsede)->get();

        //$id_almacen = $almacen[0]['id'];

        $servicios = new FuncionesController;

        //$id_ubicacion = $servicios->ubicacion_almacen_interno($id_almacen, 'Stock');
        $id_ubicacion = $request->ubicacion;

        $envio = $servicios->tipo_envio_sunat();


        $buscar = strtoupper($request->buscar_producto);

        $productos = DB::table('productos as p')
            ->leftJoin('categorias as ca', 'ca.id', '=', 'p.categoria_id')
            ->leftJoin('detalle_almacen_productos as dp', 'dp.producto_id', '=', 'p.id')
            ->leftJoin('precios as pr', 'p.id', '=', 'pr.articulo_id')
            ->select('p.id', 'p.nomb_pro', 'dp.stock', 'pr.precio_contado', 'pr.precio_credito', 'p.img', 'ca.categoria', 'dp.ubicacion_id')
            ->where('dp.ubicacion_id', '=', $id_ubicacion)
            ->where('dp.tipo_envio', '=', $envio)
            ->where('p.estado', '=', '1')
            ->where('p.nomb_pro', 'ilike', '%' . $buscar . '%')
            ->orderBy('p.id', 'asc')
            ->limit(24)
            ->get();

        return response()->json($productos);
    }

    public function consultar_existe_precio($precios, $idproducto)
    {
        $precio = array();
        foreach ($precios as $key => $value) {
            if ($idproducto == $precios->articulo_id) {
                $data = array(
                    "precio_contado" => $value->precio_contado,
                    "precio_credito" => $value->precio_credito
                );

                array_push($precio, $data);
            }
        }

        return $precio;
    }

    public function search_productos(Request $request)
    {
        $buscar = $request->buscar;

        $buscar = strtoupper($buscar);

        $productos = Productos::where('estado', '=', '1')->where('nomb_pro', 'like', "%$buscar%")->get();

        return response()->json($productos);
    }

    public function generar_venta(Request $request)
    {
        DB::beginTransaction();

        try {
            $post = $request->all();

            // Idempotencia: si la app móvil envía id_local y ya existe, devolver esa venta
            if (!empty($post['id_local'])) {
                $existente = \App\Venta::where('id_local', $post['id_local'])->first();
                if ($existente) {
                    return response()->json([
                        'id' => $existente->id,
                        'mensaje' => 'Venta ya registrada',
                        'respuesta' => 'ok',
                        'idempotente' => true,
                    ]);
                }
            }

            //echo "<pre>"; print_r($post);exit;

            if ($post['forma_pago'] == 9) {
                $montos_particionados = $post['montoParticionado'];
                $total_particionado = 0;
                for ($i = 0; $i < count($montos_particionados); $i++) {
                    $total_particionado += $montos_particionados[$i];
                }

                if ($total_particionado != $post['total_venta']) {
                    $json = array(
                        "respuesta" => "error",
                        "mensaje" => "los montos sumados particionados no es igual al total de la venta"
                    );

                    return response()->json($json);
                }
            }

            $servicios = new FuncionesController;

            $idsede = session('key')->sede_id;
            $user_id = session('key')->id;

            $serie_num = $servicios->correlativos($post['documento']);
            $serie = $serie_num->serie;
            $numero = $serie_num->correlativo;

            $num_documento = $post['numeroDocumento'];

            $desc_comp = $serie . "-" . $numero;

            $tipo_comprobante = $post['documento'];

            if ($post['tipo_venta'] == 1) {
                $desc = "VENTA AL CONTADO";
                $estado = 1;
            } else {
                $desc = "VENTA AL CREDITO";
                $estado = 0;
            }

            $consulta_cliente = Clientes::where('documento', '=', $num_documento)->first();

            $cliente = new Clientes;

            if (!$consulta_cliente) {
                $cliente->nomb_per = $post['nombre_cliente'];
                $cliente->documento = $num_documento;
                $cliente->tipo_doc = $post['tipoDocumentoIdentidad'];
                $cliente->estado_per = 1;
                $cliente->telefono = $post['celular_cliente'];
                $cliente->dire_per = $post['direccion_cliente'];
                $cliente->email = $post['correo_cliente'];
                $cliente->razon_social = $post['nombre_cliente'];
                $cliente->id_sector = empty($post['sectores']) ? null : $post['sectores'];
                $cliente->referencia = $post['referencia_cliente'] ?? null;

                $cliente->save();

                $id_cliente = $cliente->id;
            } else {
                $id_cliente = $consulta_cliente->id;

                $cliente_ = Clientes::find($id_cliente);

                $cliente_->telefono = $post['celular_cliente'];
                $cliente_->dire_per = $post['direccion_cliente'];
                $cliente_->razon_social = $post['nombre_cliente'];
                $cliente_->id_sector = empty($post['sectores']) ? null : $post['sectores'];
                $cliente_->referencia = $post['referencia_cliente'] ?? null;

                $cliente_->save();
            }

            // Si es venta a crédito, verificar primero si el cliente está en lista negra
            if ($post['tipo_venta'] == 2) {
                // Verificar si el cliente está en lista negra (de forma activa)
                $enListaNegra = DB::table('cliente_lista_negra as cln')
                    ->where('cln.cliente_id', $id_cliente)
                    ->where('cln.activo', 1)
                    ->exists();

                if ($enListaNegra) {
                    DB::rollBack();

                    // Obtener información del cliente
                    $clienteInfo = Clientes::find($id_cliente);

                    // Obtener todos los créditos activos del cliente con sus cuotas
                    $creditosActivos = Creditos::with('Detalle')
                        ->where('cliente_id', $id_cliente)
                        ->where('esta_cre', '1')
                        ->whereNull('f_anulacion')
                        ->orderBy('id', 'desc')
                        ->get();

                    $creditosData = [];
                    $totalDeuda = 0;

                    foreach ($creditosActivos as $credito) {
                        $venta = $credito->id_venta
                            ? DB::table('ventas')->where('id', $credito->id_venta)->first()
                            : null;

                        $productos = $credito->id_venta
                            ? DB::table('detalle_venta as dv')
                                ->join('productos as p', 'p.id', '=', 'dv.producto_id')
                                ->where('dv.venta_id', $credito->id_venta)
                                ->select('p.nomb_pro as nombre', 'dv.cantidad', 'dv.precio', 'dv.subtotal as importe')
                                ->get()
                            : collect();

                        $hoy = date('Y-m-d');
                        $cuotasDetalle = $credito->Detalle->map(function ($c) use ($hoy) {
                            $vencida = $c->esta_cuo === 'PENDIENTE' && $c->fven_cuo < $hoy;
                            $dias = $vencida
                                ? (int) ((strtotime($hoy) - strtotime($c->fven_cuo)) / 86400)
                                : 0;
                            return [
                                'numero'            => (int) $c->numero_cuo,
                                'fecha_vencimiento' => $c->fven_cuo,
                                'monto'             => (float) $c->mont_cuo,
                                'saldo'             => (float) $c->saldo_cuo,
                                'estado'            => $c->esta_cuo,
                                'dias_atraso'       => $dias,
                                'vencida'           => $vencida,
                            ];
                        });

                        $cuotasVencidas = $cuotasDetalle->where('vencida', true)->count();
                        $saldoPendiente = $cuotasDetalle->where('estado', 'PENDIENTE')->sum('saldo');
                        $totalDeuda += $saldoPendiente;

                        $sede = $credito->sede_id ? Sede::find($credito->sede_id) : null;

                        $creditosData[] = [
                            'id'                => $credito->id,
                            'sede'              => $sede ? $sede->nombre : 'Desconocida',
                            'fecha_credito'     => $credito->fech_cre,
                            'fecha_venta'       => $venta ? $venta->fecha : null,
                            'comprobante'       => $venta ? ($venta->serie_comprobante . '-' . $venta->numero_comprobante) : null,
                            'monto_total'       => (float) $credito->mont_cre,
                            'saldo_pendiente'   => (float) $saldoPendiente,
                            'cuotas_vencidas'   => $cuotasVencidas,
                            'cuotas_totales'    => $cuotasDetalle->count(),
                            'productos'         => $productos,
                            'cuotas'            => $cuotasDetalle->values()->all(),
                        ];
                    }

                    $json = array(
                        "respuesta"       => "error",
                        "lista_negra"     => true,
                        "mensaje"         => "El cliente se encuentra en la lista negra y no puede realizar compras al crédito.",
                        "cliente"         => [
                            "id"            => $clienteInfo->id,
                            "nombre"        => $clienteInfo->razon_social ?: ($clienteInfo->nomb_per . ' ' . $clienteInfo->pate_per . ' ' . $clienteInfo->mate_per),
                            "documento"     => $clienteInfo->documento,
                        ],
                        "total_deuda"     => (float) $totalDeuda,
                        "creditos"        => $creditosData,
                    );
                    return response()->json($json);
                }

                // Advertir si el cliente ya tiene un crédito activo en cualquier sede
                $creditoActivo = Creditos::with('Detalle')
                    ->where('cliente_id', $id_cliente)
                    ->where('esta_cre', '1')
                    ->whereNull('f_anulacion')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($creditoActivo) {
                    $sede = $creditoActivo->sede_id ? Sede::find($creditoActivo->sede_id) : null;

                    $venta = $creditoActivo->id_venta
                        ? DB::table('ventas')->where('id', $creditoActivo->id_venta)->first()
                        : null;

                    $productos = $creditoActivo->id_venta
                        ? DB::table('detalle_venta as dv')
                            ->join('productos as p', 'p.id', '=', 'dv.producto_id')
                            ->where('dv.venta_id', $creditoActivo->id_venta)
                            ->select('p.nomb_pro as nombre', 'dv.cantidad', 'dv.precio', 'dv.subtotal as importe')
                            ->get()
                        : collect();

                    $hoy = date('Y-m-d');
                    $cuotasDetalle = $creditoActivo->Detalle->map(function ($c) use ($hoy) {
                        $vencida = $c->esta_cuo === 'PENDIENTE' && $c->fven_cuo < $hoy;
                        $dias = $vencida
                            ? (int) ((strtotime($hoy) - strtotime($c->fven_cuo)) / 86400)
                            : 0;
                        return [
                            'numero'            => (int) $c->numero_cuo,
                            'fecha_vencimiento' => $c->fven_cuo,
                            'monto'             => (float) $c->mont_cuo,
                            'saldo'             => (float) $c->saldo_cuo,
                            'estado'            => $c->esta_cuo,
                            'dias_atraso'       => $dias,
                            'vencida'           => $vencida,
                        ];
                    });

                    $cuotasVencidas = $cuotasDetalle->where('vencida', true)->count();
                    $saldoPendiente = $cuotasDetalle->where('estado', 'PENDIENTE')->sum('saldo');

                    // Si el front no confirma explícitamente, devolvemos una advertencia
                    // en lugar de bloquear la venta al crédito.
                    if (empty($post['confirmar_credito'])) {
                        DB::rollBack();

                        $json = array(
                            "respuesta"      => "warning",
                            "credito_activo" => true,
                            "mensaje"        => "El cliente ya tiene un crédito activo. ¿Desea proceder con la venta al crédito de todas formas?",
                            "credito"        => array(
                                "id"              => $creditoActivo->id,
                                "sede"            => $sede ? $sede->nombre : 'Desconocida',
                                "fecha_credito"   => $creditoActivo->fech_cre,
                                "fecha_venta"     => $venta ? $venta->fecha : null,
                                "comprobante"     => $venta ? ($venta->serie_comprobante . '-' . $venta->numero_comprobante) : null,
                                "monto_total"     => (float) $creditoActivo->mont_cre,
                                "saldo_pendiente" => (float) $saldoPendiente,
                                "cuotas_vencidas" => $cuotasVencidas,
                                "cuotas_totales"  => $cuotasDetalle->count(),
                                "productos"       => $productos,
                                "cuotas"          => $cuotasDetalle->values()->all(),
                            ),
                        );
                        return response()->json($json);
                    }
                }
            }

            $envio = $servicios->tipo_envio_sunat();


            $venta = new Venta;

            $venta->moneda_id = 1;
            $venta->tipo_comprobante_id = $tipo_comprobante;
            $venta->tipo_pago_id = $post['tipo_venta'];
            $venta->user_id = $user_id;
            $venta->fecha = $post['fecha_venta'];
            $venta->hora = date('H:i:s');
            $venta->serie_comprobante = $serie;
            $venta->numero_comprobante = $numero;
            $venta->monto = $post['total_venta'];
            $venta->sede_id = $idsede;
            $venta->venta_estado = 1;
            $venta->monto_entregado = $post['total_recibido'];
            $venta->vuelto = $post['vuelto'];
            $venta->igv_monto = 0;
            $venta->monto_sin_igv = $post['total_venta'];
            $venta->tipo_envio = $envio;
            $venta->cliente_id = $id_cliente;
            $venta->descuento = '0';
            // Si es venta móvil, $post['vendedor'] contiene users.id, necesitamos vendedores.id
            if (isset($post['es_movil']) && $post['es_movil']) {
                $vendedor = Vendedor::where('usuario_id', $post['vendedor'])->first();
                $venta->vendedor_id = $vendedor ? $vendedor->id : 1;
            } else {
                $venta->vendedor_id = $post['vendedor'] ?? 1;
            }
            $venta->id_local = $post['id_local'] ?? null;
            $venta->fecha_offline_created = !empty($post['fecha_offline_created']) ? $post['fecha_offline_created'] : null;
            if (isset($post['es_movil']) && $post['es_movil']) {
                $venta->estado_liquidacion = 'PENDIENTE';
            } else {
                $venta->estado_liquidacion = 'NO_APLICA';
            }

            $venta->save();

            $id_venta = $venta->id;

            // Si es venta al crédito, crear registro de crédito y cuotas
            if ($post['tipo_venta'] == 2 && !empty($post['cuotas_data'])) {
                $cuotasData = json_decode($post['cuotas_data'], true);

                // Crear registro de crédito
                $credito = new Creditos;
                $credito->mont_cre = $post['total_venta'];
                $credito->esta_cre = '1';
                $credito->fech_cre = date('Y-m-d');
                $credito->inte_cre = 0;
                $credito->impo_cre = $post['total_venta'];
                $credito->fpag_cre = $cuotasData[0]['fecha_vencimiento'] ?? date('Y-m-d'); // Fecha primer pago
                $credito->peri_cre = count($cuotasData);
                $credito->cliente_id = $id_cliente;
                $credito->obse_cre = '';
                $credito->usuario = $user_id;
                $credito->tipo_doc = $post['tipoDocumentoIdentidad'] ?? 1;
                $credito->id_venta = $id_venta;
                $credito->periodo_pago = 'MENSUAL';
                $credito->sede_id = $idsede;
                $credito->id_con = $post['concepto_credito_id'] ?? 1;
                $credito->save();

                // Crear cuotas
                foreach ($cuotasData as $cuotaInfo) {
                    $cuota = new Cuotas;
                    $cuota->mont_cuo = $cuotaInfo['monto'];
                    $cuota->fven_cuo = $cuotaInfo['fecha_vencimiento'];
                    $cuota->saldo_cuo = $cuotaInfo['monto'];
                    $cuota->capi_cuo = $cuotaInfo['monto'];
                    $cuota->credito_id = $credito->id;
                    $cuota->esta_cuo = 'PENDIENTE';
                    $cuota->numero_cuo = $cuotaInfo['numero'];
                    $cuota->sald_cap = $cuotaInfo['monto'];
                    $cuota->version = 1;
                    $cuota->save();
                }

                // Actualizar estado de venta a "crédito registrado"
                $venta->venta_estado = 2;
                $venta->save();
            }

            $cantidades = $post['quanty'];
            $productos = $post['idproducto'];
            $precios = $post['priceproducto'];
            $descripcion = $post['nameproducto'];
            $subtotal = $post['importe'];
            $ubicaciones = $post['ubicacion'];

            $hasta = count($cantidades);

            for ($i = 0; $i < $hasta; $i++) {
                $es_movil = isset($post['es_movil']) && $post['es_movil'];

                // Validar stock disponible para ventas móviles ANTES de guardar
                if ($es_movil && $tipo_comprobante != 9) {
                    $fechaHoy = date('Y-m-d');
                    // Para móviles, usamos users.id para buscar en stock_vendedor
                    $usersId = !empty($post['vendedor']) ? $post['vendedor'] : Auth::user()->id;
                    $productoId = $productos[$i];
                    $cantidadSolicitada = $cantidades[$i];

                    // Obtener stock disponible de stock_vendedor
                    $stockDisponible = DB::table('stock_vendedor')
                        ->where('vendedor_id', $usersId)
                        ->where('producto_id', $productoId)
                        ->where('fecha_carga', $fechaHoy)
                        ->where('estado', 1)
                        ->sum('cantidad_disponible') ?? 0;

                    if ($cantidadSolicitada > $stockDisponible) {
                        $nombreProducto = $descripcion[$i] ?? "Producto ID $productoId";
                        DB::rollBack();
                        $json = array(
                            "respuesta" => "error",
                            "mensaje" => "Stock insuficiente para el producto \"$nombreProducto\". Disponible: $stockDisponible, Solicitado: $cantidadSolicitada"
                        );
                        return response()->json($json);
                    }
                }

                $detalle = new Detalle_venta;

                $detalle->producto_id = $productos[$i];
                $detalle->venta_id = $id_venta;
                $detalle->cantidad = $cantidades[$i];
                $detalle->precio = $precios[$i];
                $detalle->subtotal = $subtotal[$i];
                $detalle->descripcion = $descripcion[$i];
                $detalle->ubicacion_id = $ubicaciones[$i];

                $detalle->save();

                // Solo NO descuenta stock si es cotización (id=9)
                if ($tipo_comprobante != 9) {
                    \Log::info("DESCUENTO STOCK", [
                        'es_movil' => $es_movil,
                        'tipo_comprobante' => $tipo_comprobante,
                        'ubicacion_id' => $ubicaciones[$i],
                        'producto_id' => $productos[$i],
                        'cantidad' => $cantidades[$i],
                        'tipo_envio' => $envio,
                    ]);

                    if ($es_movil) {
                        // Para ventas móviles, descontar de stock_vendedor
                        $fechaHoy = date('Y-m-d');
                        $vendedorId = $post['vendedor'];

                        // Descontar del stock_vendedor (primero en entrar, primero en salir - FIFO)
                        $stockVendedorItems = DB::table('stock_vendedor')
                            ->where('vendedor_id', $vendedorId)
                            ->where('producto_id', $productos[$i])
                            ->where('fecha_carga', $fechaHoy)
                            ->where('estado', 1)
                            ->where('cantidad_disponible', '>', 0)
                            ->orderBy('id', 'asc')
                            ->get();

                        $cantidadRestante = $cantidades[$i];

                        foreach ($stockVendedorItems as $item) {
                            if ($cantidadRestante <= 0) break;

                            if ($item->cantidad_disponible >= $cantidadRestante) {
                                // Descontar del item actual
                                DB::table('stock_vendedor')
                                    ->where('id', $item->id)
                                    ->update([
                                        'cantidad_vendida' => $item->cantidad_vendida + $cantidadRestante,
                                        'cantidad_disponible' => $item->cantidad_disponible - $cantidadRestante,
                                    ]);
                                $cantidadRestante = 0;
                            } else {
                                // Descontar todo lo disponible del item actual y continuar con el siguiente
                                $cantidadRestante -= $item->cantidad_disponible;
                                DB::table('stock_vendedor')
                                    ->where('id', $item->id)
                                    ->update([
                                        'cantidad_vendida' => $item->cantidad_vendida + $item->cantidad_disponible,
                                        'cantidad_disponible' => 0,
                                    ]);
                            }
                        }

                        if ($cantidadRestante > 0) {
                            \Log::warning("Stock insuficiente al descontar stock_vendedor", [
                                'vendedor_id' => $vendedorId,
                                'producto_id' => $productos[$i],
                                'cantidad_solicitada' => $cantidades[$i],
                                'cantidad_restante_sin_descontar' => $cantidadRestante,
                            ]);
                        }
                    } else {
                        // Para ventas normales, descontar de detalle_almacen_productos
                        $servicios->aumentar_descontar_stock(0, $ubicaciones[$i], $productos[$i], $cantidades[$i], $envio);
                    }
                    $servicios->movimiento_kardex_producto($ubicaciones[$i], $productos[$i], $cantidades[$i], 2, "VENTA " . $serie . "-" . $numero, $serie, $numero, $precios[$i], $tipo_comprobante, date('Y-m-d'), date('Y-m-d'));
                }
            }

            if (isset($post['es_movil']) && $post['es_movil']) {
                $estado_mov = 0;
            } else {
                if ($post['tipo_venta'] == 1) {
                    $estado_mov = 1;
                } else {
                    $estado_mov = 0;
                }
            }

            // Registrar pago de inicial si existe (para ventas al crédito)
            $cuotaInicial = !empty($post['cuota_inicial']) ? floatval($post['cuota_inicial']) : 0;
            if ($cuotaInicial > 0 && $post['tipo_venta'] == 2) {
                $inicialFormaPago = !empty($post['inicial_forma_pago']) ? $post['inicial_forma_pago'] : 1;
                $inicialOperacion = !empty($post['inicial_numero_operacion']) ? $post['inicial_numero_operacion'] : '';

                $movimientoInicial = $servicios->generar_movimiento("INGRESO", $inicialFormaPago, 9, $cuotaInicial, "PAGO INICIAL CREDITO", $tipo_comprobante, 1, $serie . "-" . $numero, 0);

                $formaInicial = new Venta_formapago;
                $formaInicial->venta_id = $id_venta;
                $formaInicial->forma_pago_id = $inicialFormaPago;
                $formaInicial->monto = $cuotaInicial;
                $formaInicial->numero_operacion = $inicialOperacion;
                $formaInicial->banco_id = null;
                $formaInicial->movimiento_id = $movimientoInicial;
                $formaInicial->save();
            }

            // Para ventas al crédito, no crear registro de forma_pago (se maneja con cuota_inicial)
            if ($post['forma_pago'] != 9 && $post['tipo_venta'] != 2) {
                $forma_venta = new Venta_formapago;

                $movimiento_id = $servicios->generar_movimiento("INGRESO", $post['forma_pago'], 9, $post['total_venta'], "VENTA DE MERCADERIA", $tipo_comprobante, 1, $serie . "-" . $numero, $estado_mov);

                $forma_venta->venta_id = $id_venta;
                $forma_venta->forma_pago_id = $post['forma_pago'];
                $forma_venta->monto = $post['total_venta'];
                $forma_venta->numero_operacion = $post['numero_operacion'] ?? null;
                $forma_venta->banco_id = $post['banco_venta'] ?? null;
                $forma_venta->movimiento_id = $movimiento_id;

                $forma_venta->save();
            } elseif (isset($post['forma_pago_particionado'])) {
                // Solo procesar si existe el campo particionado
                $formaId = $post['forma_pago_particionado'];
                $formaName = $post['formaPagoParticionado'];
                $formaMonto = $post['montoParticionado'];
                $formaNumero = $post['numeroOperacionParticionado'];
                $formaBanco = $post['bancoParticionado'];

                for ($i = 0; $i < count($formaId); $i++) {
                    $movimiento_id = $servicios->generar_movimiento("INGRESO", $formaId[$i], 9, $formaMonto[$i], "VENTA DE MERCADERIA", $tipo_comprobante, 1, $serie . "-" . $numero, $estado_mov);

                    $forma_venta = new Venta_formapago;
                    $forma_venta->venta_id = $id_venta;
                    $forma_venta->forma_pago_id = $formaId[$i];
                    $forma_venta->monto = $formaMonto[$i];
                    $forma_venta->numero_operacion = $formaNumero[$i] ?? null;
                    $forma_venta->banco_id = $formaBanco[$i] ?? null;
                    $forma_venta->movimiento_id = $movimiento_id;
                    $forma_venta->save();
                }
            }

            // Guardar firma del cliente si existe (ventas móviles al crédito)
            if (!empty($post['firma'])) {
                \Log::info('Procesando firma', [
                    'venta_id' => $id_venta,
                    'firma_length' => strlen($post['firma']),
                    'firma_preview' => substr($post['firma'], 0, 50) . '...',
                ]);
                try {
                    $firmaData = base64_decode($post['firma']);
                    if ($firmaData === false) {
                        \Log::error('Error al decodificar base64 de la firma', ['venta_id' => $id_venta]);
                    } else {
                        $firmaPath = 'firmas/venta_' . $id_venta . '.png';
                        $stored = Storage::disk('public')->put($firmaPath, $firmaData);
                        \Log::info('Resultado storage', ['venta_id' => $id_venta, 'stored' => $stored, 'path' => $firmaPath]);
                        $venta->firma = $firmaPath;
                        $venta->save();
                        \Log::info('Firma guardada exitosamente', ['venta_id' => $id_venta, 'path' => $firmaPath]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error al guardar firma', [
                        'venta_id' => $id_venta,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            $json = array(
                "id" => $id_venta,
                "credito_id" => isset($credito) ? $credito->id : null,
                "mensaje" =>  "Se registro correctamente la venta",
                "respuesta" => "ok"
            );

            DB::commit();

            return response()->json($json);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('generar_venta exception', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return response()->json([
                'respuesta' => 'error',
                'mensaje'   => $e->getMessage(),
                'linea'     => $e->getLine(),
                'archivo'   => $e->getFile(),
            ]);
        }
    }

    private function getUbicacionMoviles($idsede)
    {
        $almacenPrincipal = \App\Almacen::where('sede_id', $idsede)->first();
        if (!$almacenPrincipal) return null;
        $ubicacion = DB::table('stock_location')
            ->where('almacen_id', $almacenPrincipal->id)
            ->where(DB::raw('LOWER(name)'), 'moviles')
            ->first();
        return $ubicacion ? $ubicacion->id : null;
    }

    public function consultar_dni_ruc(Request $request)
    {
        try {
            //consultamos en l base de datos si existe

            $data_cliente = Clientes::where('documento', '=', $request->num_doc)->first();

            if ($data_cliente) {
                $data = array(
                    "exception" => "existe_base_datos",
                    "original" => [
                        "nombres" => $data_cliente->razon_social,
                        "direccion" => $data_cliente->dire_per,
                        "celular" => $data_cliente->telefono
                    ]
                );

                return response()->json($data);
            } else {
                //sino existe consultamos a la api
                $data = new FuncionesController;

                $consulta = $data->consultar_ruc_dni($request->tipo_documento, $request->num_doc);

                return response()->json($consulta);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function ticket($id)
    {
        $empresa = Empresa::first();

        $venta = DB::table('ventas')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->join('users', 'users.id', '=', 'ventas.user_id')
            ->join('tipo_comprobantes', 'ventas.tipo_comprobante_id', '=', 'tipo_comprobantes.id')
            ->where('ventas.id', '=', $id)
            ->first();

        $detalle = DB::table('detalle_venta')
            ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
            ->where('detalle_venta.venta_id', '=', $id)
            ->get();

        //echo "<pre>"; print_r($venta);exit;

        define('EURO', chr(128));

        $pdf = new Fpdf('P', 'mm', array(80, 297));
        $pdf->AddPage();

        // CABECERA
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Image('../public/img/logo2.jpeg', 20, 5, 40, 28);
        $pdf->Ln(28);
        $pdf->MultiCell(60, 4, $empresa['razon_social'], 0, 'C');
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->MultiCell(60, 4, $empresa['nombre_comercial'], 0, 'C');

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->MultiCell(60, 4, utf8_decode($empresa['direccion_fiscal']), 0, 'C');

        $pdf->MultiCell(60, 4, "RUC: " . $empresa['ruc'], 0, 'C');
        $pdf->Ln(1);

        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(60, 4, utf8_decode($venta->descripcion), 0, 'C');
        $pdf->MultiCell(60, 4, utf8_decode($venta->serie_comprobante . "-" . $venta->numero_comprobante), 0, 'C');
        $pdf->SetFont('Helvetica', '', 8);

        // DATOS FACTURA
        $pdf->Ln(5);
        $pdf->MultiCell(60, 4, 'CLIENTE: ' . utf8_decode($venta->razon_social), 0, '');
        $pdf->MultiCell(60, 4, 'DNI/RUC: ' . $venta->documento, 0, '');
        $pdf->MultiCell(60, 4, utf8_decode('DIRECCIÓN: ' . $venta->dire_per), 0, '');

        // COLUMNAS
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->Cell(28, 10, utf8_decode('Descripción'), 0);
        $pdf->Cell(5, 10, 'Und', 0, 0, 'R');
        $pdf->Cell(10, 10, 'Precio', 0, 0, 'R');
        $pdf->Cell(15, 10, 'Total', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(60, 0, '', 'T');
        $pdf->Ln(2);

        // PRODUCTOS
        $pdf->SetFont('Helvetica', '', 8);

        $total_venta = 0;

        foreach ($detalle as $key => $value) {

            $total_venta += $value->cantidad * $value->precio;

            $pdf->MultiCell(30, 4, utf8_decode($value->nomb_pro), 0, 'L');
            $pdf->Cell(32, -5, $value->cantidad, 0, 0, 'R');
            $pdf->Cell(15, -5, number_format($value->precio, 2, ',', ' '));
            $pdf->Cell(15, -5, "S/ " . number_format($value->precio * $value->cantidad, 2, ',', ' '));
            $pdf->Ln(2);
        }

        // SUMATORIO DE LOS PRODUCTOS Y EL IVA
        $pdf->Ln(2);
        $pdf->Cell(60, 0, '', 'T');
        $pdf->Ln(2);
        $pdf->Cell(25, 10, 'SUBTOTAL', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, "S/ " . number_format($total_venta, 2, ',', ' '));
        $pdf->Ln(3);
        $pdf->Cell(25, 10, 'DESCUENTO', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, "S/ " . number_format(0, 2, ',', ' '));
        $pdf->Ln(3);
        $pdf->Cell(25, 10, 'VALOR VENTA', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, "S/ " . number_format($total_venta, 2, ',', ' '));
        $pdf->Ln(3);
        $pdf->Cell(25, 10, 'IGV', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, "S/ " . number_format(0, 2, ',', ' '));
        $pdf->Ln(5);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->Cell(25, 10, 'TOTAL', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, "S/ " . number_format($total_venta, 2, ',', ' '));

        $pdf->SetFont('Helvetica', '', 8);

        // PIE DE PAGINA
        $pdf->Ln(10);
        //$pdf->MultiCell(60,4,'SON: '.$letras,0,'');
        $pdf->MultiCell(60, 4, 'FECHA: ' . date('d-m-Y', strtotime($venta->fecha)), 0, '');
        $pdf->MultiCell(60, 4, 'ATENDIDO POR: ' . utf8_decode($venta->name), 0, '');

        ob_get_clean();

        $pdf->Output('ticket.pdf', 'I');
    }

    public function ticketA4($id)
    {
        $empresa = Empresa::first();

        $venta = DB::table('ventas')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->join('users', 'users.id', '=', 'ventas.user_id')
            ->join('tipo_comprobantes', 'ventas.tipo_comprobante_id', '=', 'tipo_comprobantes.id')
            ->where('ventas.id', '=', $id)
            ->first();

        $detalle = DB::table('detalle_venta')
            ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
            ->where('detalle_venta.venta_id', '=', $id)
            ->get();

        $pdf = new Fpdf('P', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFillColor(242, 242, 242);
        $pdf->Rect(0, 0, 210, 40, 'F');

        if (file_exists(public_path('img/logo2.jpeg'))) {
            $pdf->Image(public_path('img/logo2.jpeg'), 20, 10, 45, 30);
        }

        $pdf->SetXY(70, 12);
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(115, 8, utf8_decode($empresa['nombre_comercial']), 0, 1, 'L');
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetX(70);
        $pdf->Cell(115, 6, utf8_decode($empresa['razon_social']), 0, 1, 'L');
        $pdf->SetX(70);
        $pdf->Cell(115, 6, utf8_decode($empresa['direccion_fiscal']), 0, 1, 'L');
        $pdf->SetX(70);
        $pdf->Cell(115, 6, 'RUC: ' . $empresa['ruc'], 0, 1, 'L');
        $pdf->Ln(4);

        $pdf->SetDrawColor(102, 102, 102);
        $pdf->SetLineWidth(0.4);
        $pdf->Line(15, 42, 195, 42);
        $pdf->Ln(6);

        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetFillColor(52, 73, 94);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 10, utf8_decode($venta->descripcion . ' - ' . $venta->serie_comprobante . '-' . $venta->numero_comprobante), 0, 1, 'C', true);
        $pdf->Ln(4);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(95, 6, 'CLIENTE: ' . utf8_decode($venta->razon_social), 0, 0, 'L');
        $pdf->Cell(0, 6, 'FECHA: ' . date('d/m/Y', strtotime($venta->fecha)), 0, 1, 'R');
        $pdf->Cell(95, 6, 'DNI/RUC: ' . $venta->documento, 0, 0, 'L');
        $pdf->Cell(0, 6, 'HORA: ' . substr($venta->hora, 0, 5), 0, 1, 'R');
        $pdf->MultiCell(0, 6, utf8_decode('DIRECCIÓN: ' . $venta->dire_per), 0, 'L');
        $pdf->Ln(4);

        $pdf->SetFillColor(232, 236, 241);
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(95, 8, utf8_decode('DESCRIPCIÓN'), 1, 0, 'L', true);
        $pdf->Cell(25, 8, 'CANT.', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'P.UNIT.', 1, 0, 'R', true);
        $pdf->Cell(35, 8, 'TOTAL', 1, 1, 'R', true);

        $pdf->SetFont('Helvetica', '', 10);
        $total_venta = 0;

        foreach ($detalle as $value) {
            $subtotal = $value->cantidad * $value->precio;
            $total_venta += $subtotal;
            $pdf->Cell(95, 7, utf8_decode(substr($value->nomb_pro, 0, 60)), 1, 0, 'L');
            $pdf->Cell(25, 7, $value->cantidad, 1, 0, 'C');
            $pdf->Cell(35, 7, 'S/ ' . number_format($value->precio, 2, ',', ' '), 1, 0, 'R');
            $pdf->Cell(35, 7, 'S/ ' . number_format($subtotal, 2, ',', ' '), 1, 1, 'R');
        }

        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(95, 7, '', 0, 0, 'R');
        $pdf->Cell(25, 7, '', 0, 0, 'R');
        $pdf->Cell(35, 7, 'SUBTOTAL', 1, 0, 'R');
        $pdf->Cell(35, 7, 'S/ ' . number_format($total_venta, 2, ',', ' '), 1, 1, 'R');
        $pdf->Cell(95, 7, '', 0, 0, 'R');
        $pdf->Cell(25, 7, '', 0, 0, 'R');
        $pdf->Cell(35, 7, 'DESCUENTO', 1, 0, 'R');
        $pdf->Cell(35, 7, 'S/ 0.00', 1, 1, 'R');
        $pdf->Cell(95, 7, '', 0, 0, 'R');
        $pdf->Cell(25, 7, '', 0, 0, 'R');
        $pdf->Cell(35, 7, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(35, 7, 'S/ ' . number_format($total_venta, 2, ',', ' '), 1, 1, 'R');

        $pdf->Ln(10);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode('Gracias por su compra. Si necesita el comprobante en formato electrónico, contáctenos por sus canales oficiales.'), 0, 'L');
        $pdf->Ln(4);
        $pdf->Cell(0, 6, 'ATENDIDO POR: ' . utf8_decode($venta->name), 0, 1, 'L');

        ob_get_clean();
        $pdf->Output('venta_a4.pdf', 'I');
    }

    public function traer_candado($monto)
    {
        $candados = candado::where('rango_minimo', '<=', $monto)->where('rango_maximo', '>=', $monto)->first();

        return response()->json($candados);
    }

    public function listCategories()
    {
        $data = Categorias::where('estado', '=', 1)->get();
        return response()->json($data);
    }

    public function traer_comprobantes_venta()
    {
        $data = Tipo_comprobantes::where('id', '=', 1)->orwhere('id', '=', 2)->orwhere('id', '=', 5)->orwhere('id', '=', 1)->orwhere('id', '=', 9)->get();
        return response()->json($data);
    }

    public function traer_documento_identidad()
    {
        $data = Tipo_documento::all();
        return response()->json($data);
    }

    public function forma_pago()
    {
        $data = DB::table('forma_pagos')->orderBy('id', 'asc')->get();
        return response()->json($data);
    }

    public function bancos_ventas()
    {
        $data = DB::table('cuentas_bancarias as cb')->join('bancos as b', 'b.id', '=', 'cb.banco_id')->select('cb.id', 'cb.cuenta_corriente', 'b.abreviatura')->get();
        return response()->json($data);
    }

    public function traer_precios(Request $request)
    {
        $idsede = session('key')->sede_id;
        $idproducto = $request->idproducto;

        //$data = Precios::where('articulo_id', '=', $idproducto)->where('sucursal_id', '=', $idsede)->where('lista_id', '=', 3)->first();

        $data = Precios::where('articulo_id', '=', $idproducto)->where('lista_id', '=', 3)->first();

        return response()->json($data);
    }

    public function enviar_comprobante($id)
    {

        $idsede = session('key')->sede_id;
        $sede = Sede::find($idsede);

        $empresa = Empresa::find($sede->empresa_id);

        $ubigeoId = $empresa->ubigeo_id;

        $ruc = $empresa->ruc;
        $razon_social = $empresa->razon_social;
        $nombre_comercial = $empresa->nombre_comercial;
        $direccion_fiscal = $empresa->direccion_fiscal;
        $usuarioSol = $empresa->usuario_sol;
        $claveSol = $empresa->clave_sol;
        $claveCertificado = $empresa->password_certificado;

        $ubigeo = Ubigeo::find($ubigeoId);

        $codigoUbigeo = $ubigeo->codigo_ubigeo;
        $departamento = $ubigeo->departamento;
        $provincia = $ubigeo->provincia;
        $distrito = $ubigeo->distrito;

        $venta = Venta::find($id);

        $clienteId = $venta->cliente_id;

        $cliente = Clientes::find($clienteId);

        $detalleVenta = Detalle_venta::where('venta_id', '=', $id)->get();

        $dataDetalle = [];

        foreach ($detalleVenta as $key => $value) {
            $detalle = array(
                "txtITEM"                   => ($key + 1),
                "txtUNIDAD_MEDIDA_DET"      => "NIU",
                "txtCANTIDAD_DET"           => number_format($value->cantidad, 2, '.', ''),
                "txtPRECIO_DET"             => number_format($value->precio, 2, '.', ''),
                "txtSUB_TOTAL_DET"          => number_format($value->subtotal, 2, '.', ''),
                "txtPRECIO_TIPO_CODIGO"     => "01",
                "txtIGV"                    => "0.00",
                "txtISC"                    => "0.00",
                "txtIMPORTE_DET"            => number_format($value->subtotal, 2, '.', ''),
                "txtCOD_TIPO_OPERACION"     => "20",
                "txtCODIGO_DET"             => "DSDFG",
                "txtDESCRIPCION_DET"        => $value->descripcion,
                "txtPRECIO_SIN_IGV_DET"     => number_format($value->precio, 2, '.', ''),
                "txtESTADO_ICBPER"          => "0.00",
                "textITEM_DESCUENTO"        => "0.00",
                "textMONTO_BASE"            => $value->precio,
                "textFACTOR"                => "0.00",
                "txtCODIGO_PROD_SUNAT"      => '23251602' //CODIGO SUNAT
            );

            array_push($dataDetalle, $detalle);
        }

        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $envio == 0 ? $enviar = "3" : $enviar = "1";

        $montoLetras = new NumberFormatter('es', NumberFormatter::SPELLOUT);

        $montoLetras = ucfirst($montoLetras->format($venta->monto));

        $venta->tipo_comprobante_id == 1 ? $tipo_documento = "03" : $tipo_documento = "01";
        $venta->tipo_comprobante_id == 1 ? $cliente_documento = "1" : $cliente_documento = "6";
        $venta->tipo_comprobante_id == 1 ? $ruta = "https://esconsultoresyasesores.com:9091/api_facturacion/boleta.php" : $ruta = "http://157.230.239.170/sis_facturacion/api_facturacion/factura.php";

        $data = array(

            //Cabecera del documento
            "tipo_operacion"                => "0101",
            "total_gravadas"                => "0.00",
            "total_inafecta"                => "0.00",
            "total_exoneradas"              => number_format($venta->monto, 2, '.', ''),
            "total_gratuitas"               => "0.00",
            "total_exportacion"             => "0.00",
            "total_descuento"               => "0.00",
            "sub_total"                     => number_format($venta->monto, 2, '.', ''),
            "porcentaje_igv"                => "0.00",
            "total_igv"                     => "0.00",
            "total_isc"                     => "0.00",
            "total_otr_imp"                 => "0.00",
            "total"                         => number_format($venta->monto, 2, '.', ''),
            "total_letras"                  => $montoLetras,
            "nro_guia_remision"             => "",
            "cod_guia_remision"             => "",
            "nro_otr_comprobante"           => "",
            "serie_comprobante"             => $venta->serie_comprobante, //Para Facturas la serie debe comenzar por la letra F, seguido de tres dígitos
            "numero_comprobante"            => $venta->numero_comprobante,
            "fecha_comprobante"             => date('Y-m-d', strtotime($venta->fecha)),
            "fecha_vto_comprobante"         => date('Y-m-d', strtotime($venta->fecha)),
            "cod_tipo_documento"            => $tipo_documento,
            "cod_moneda"                    => "PEN",
            "tipo_proceso"                  => $enviar,
            "pass_firma"                    => $claveCertificado,
            "monto_icbper"                  => "0.00",
            "impuesto_icbper"               => "0.00",
            "tipo_pago"                     => "1",
            "cuotas"                        => [],
            "anexo"                            => $sede->anexo,

            //Datos del cliente
            "cliente_numerodocumento"       => $cliente->documento,
            "cliente_nombre"                => $cliente->razon_social,
            "cliente_tipodocumento"         => $cliente_documento, //6: RUC
            "cliente_direccion"             => $cliente->dire_per,
            "cliente_pais"                  => "PE",
            "cliente_ciudad"                => "",
            "cliente_codigoubigeo"          => "",
            "cliente_departamento"          => "",
            "cliente_provincia"             => "",
            "cliente_distrito"              => "",

            //data de la empresa emisora o contribuyente que entrega el documento electrónico.
            "emisor" => array(
                "ruc"                       => $ruc,
                "tipo_doc"                  => "6",
                "nom_comercial"             => $nombre_comercial,
                "razon_social"              => $razon_social,
                "codigo_ubigeo"             => $codigoUbigeo,
                "direccion"                 => $direccion_fiscal,
                "direccion_departamento"    => strtoupper($departamento),
                "direccion_provincia"       => strtoupper($provincia),
                "direccion_distrito"        => strtoupper($distrito),
                "direccion_codigopais"      => "PE",
                "usuariosol"                => $usuarioSol,
                "clavesol"                  => $claveSol
            ),

            //items del documento
            "detalle" => $dataDetalle,
            "ruta" => $ruta,
            "idsale" => $id
        );

        //Invocamos el servicio
        $token = ''; //en caso quieras utilizar algún token generado desde tu sistema

        //codificamos la data
        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($respuesta, true);

        if ($response == "null" || $response == "") {
            $response['respuesta'] = "error";
            $response['mensaje'] = "Ocurrio un error al conectarse con Sunat";
            return response()->json($response);
        }

        if ($response['respuesta'] == "error") {
            return response()->json($response);
        }

        $mensaje = $response['msj_sunat'];

        $update = Venta::find($id);

        $update->aceptado_sunat = "1";
        $update->mensaje_sunat = $mensaje;
        $update->cod_sunat = $response['cod_sunat'];
        $update->hash_cdr = $response['hash_cdr'];
        $update->hash_cpe = $response['hash_cpe'];

        $update->save();

        return response()->json($response);
    }

    public function generarNotaCredito(Request $request)
    {
        $id = $request->idventa;
        $motivo = $request->motivo;

        $nota = $this->enviarNotaCredito($id);

        try {
            if ($nota['nota_credito']['respuesta'] == 'ok') {
                $venta = venta::find($id);
                $venta->fecha_eliminacion = date('Y-m-d');
                $venta->user_eliminacion = session('key')->id;
                $venta->serie_nota_credito = $nota['serie'];
                $venta->numero_nota_credito = $nota['correlativo'];
                $venta->estado_nota = 2;
                $venta->save();

                $detalle_venta = Detalle_venta::where('venta_id', '=', $id)->get();

                $new_venta = new venta;

                $new_venta->moneda_id = 1;
                $new_venta->tipo_comprobante_id = 3;
                $new_venta->tipo_pago_id = $venta->tipo_pago_id;
                $new_venta->user_id = $venta->user_id;
                $new_venta->fecha = date('Y-m-d');
                $new_venta->hora = date('H:i:s');
                $new_venta->serie_comprobante = $nota['serie'];
                $new_venta->numero_comprobante = $nota['correlativo'];
                $new_venta->monto = $venta->monto;
                $new_venta->sede_id = $venta->sede_id;
                $new_venta->venta_estado = 1;
                $new_venta->monto_entregado = $venta->monto_entregado;
                $new_venta->vuelto = $venta->vuelto;
                $new_venta->igv_monto = 0;
                $new_venta->monto_sin_igv = $venta->monto_sin_igv;
                $new_venta->tipo_envio = $venta->tipo_envio;
                $new_venta->cliente_id = $venta->cliente_id;
                $new_venta->descuento = '0';
                $new_venta->vendedor_id = $venta->vendedor_id;
                $new_venta->aceptado_sunat = 1;
                $new_venta->mensaje_sunat = $nota['nota_credito']['msj_sunat'];
                $new_venta->cod_sunat = $nota['nota_credito']['cod_sunat'];
                $new_venta->hash_cdr = $nota['nota_credito']['cod_sunat'];
                $new_venta->hash_cpe = $nota['nota_credito']['hash_cpe'];

                $new_venta->save();

                $query_id = Venta::orderBy('id', 'asc')->get();
                $ultimo_idventa = $query_id->last();

                $id_venta = $ultimo_idventa['id'];

                $servicios = new FuncionesController;

                foreach ($detalle_venta as $key => $value) {
                    $detalle = new Detalle_venta;

                    $detalle->producto_id = $value->producto_id;
                    $detalle->venta_id = $id_venta;
                    $detalle->cantidad = $value->cantidad;
                    $detalle->precio = $value->precio;
                    $detalle->subtotal = $value->subtotal;
                    $detalle->descripcion = $value->descripcion;
                    $detalle->ubicacion_id = $value->ubicacion_id;

                    $detalle->save();

                    $servicios->aumentar_descontar_stock(1, $value->ubicacion_id, $value->producto_id, $value->cantidad, $venta->tipo_envio);
                    $servicios->movimiento_kardex_producto($value->ubicacion_id, $value->producto_id, $value->cantidad, 1, "NOTA DE CREDITO", $nota['serie'], $nota['correlativo'], $value->precio, 3, date('Y-m-d'), date('Y-m-d'));
                }

                $num = Correlativos::find($nota['idCorrelativo']);
                $num->correlativo = $nota['correlativo'] + 1;
                $num->save();

                $json = array(
                    "respuesta" => "ok",
                    "mensaje" => $nota['nota_credito']['msj_sunat']
                );

                return response()->json($json);
            } else {
                $json = array(
                    "respuesta" => "error",
                    "mensaje" => "Intente de nuevo o comuniquese con el administrador del sistema"
                );

                return response()->json($json);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function enviarNotaCredito($id)
    {
        $idsede = session('key')->sede_id;
        $sede = Sede::find($idsede);

        $empresa = Empresa::find($sede->empresa_id);

        $ubigeoId = $empresa->ubigeo_id;

        $ruc = $empresa->ruc;
        $razon_social = $empresa->razon_social;
        $nombre_comercial = $empresa->nombre_comercial;
        $direccion_fiscal = $empresa->direccion_fiscal;
        $usuarioSol = $empresa->usuario_sol;
        $claveSol = $empresa->clave_sol;
        $claveCertificado = $empresa->password_certificado;

        $ubigeo = Ubigeo::find($ubigeoId);

        $codigoUbigeo = $ubigeo->codigo_ubigeo;
        $departamento = $ubigeo->departamento;
        $provincia = $ubigeo->provincia;
        $distrito = $ubigeo->distrito;

        $venta = Venta::find($id);

        $clienteId = $venta->cliente_id;

        $cliente = Clientes::find($clienteId);

        $detalleVenta = Detalle_venta::where('venta_id', '=', $id)->get();

        $dataDetalle = [];

        foreach ($detalleVenta as $key => $value) {
            $detalle = array(
                "txtITEM"                   => ($key + 1),
                "txtUNIDAD_MEDIDA_DET"      => "NIU",
                "txtCANTIDAD_DET"           => number_format($value->cantidad, 2, '.', ''),
                "txtPRECIO_DET"             => number_format($value->precio, 2, '.', ''),
                "txtSUB_TOTAL_DET"          => number_format($value->subtotal, 2, '.', ''),
                "txtPRECIO_TIPO_CODIGO"     => "01",
                "txtIGV"                    => "0.00",
                "txtISC"                    => "0.00",
                "txtIMPORTE_DET"            => number_format($value->subtotal, 2, '.', ''),
                "txtCOD_TIPO_OPERACION"     => "20",
                "txtCODIGO_DET"             => "DSDFG",
                "txtDESCRIPCION_DET"        => $value->descripcion,
                "txtPRECIO_SIN_IGV_DET"     => number_format($value->precio, 2, '.', ''),
                "txtESTADO_ICBPER"          => "0.00",
                "textITEM_DESCUENTO"        => "0.00",
                "textMONTO_BASE"            => $value->precio,
                "textFACTOR"                => "0.00",
                "txtCODIGO_PROD_SUNAT"      => '23251602' //CODIGO SUNAT
            );

            array_push($dataDetalle, $detalle);
        }

        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $envio == 0 ? $enviar = "3" : $enviar = "1";

        $montoLetras = new NumberFormatter('es', NumberFormatter::SPELLOUT);

        $montoLetras = ucfirst($montoLetras->format($venta->monto));

        $ruta = "https://esconsultoresyasesores.com:9091/api_facturacion/notacredito.php";

        if ($venta->tipo_comprobante_id == 1) {
            $denominacion = "Boleta Electrónica";
            $tipo_comprobante_modifica = "03";
            $cliente_documento = "1";
            $letter = "B";
        } else {
            $denominacion = "Factura Electrónica";
            $tipo_comprobante_modifica = "01";
            $cliente_documento = "6";
            $letter = "F";
        }

        $correlativo_ = Correlativos::where('sede_id', '=', $idsede)->where('tipo_comprobante_id', '=', 3)->where('tipo_envio', '=', $envio)->where('serie', 'like', '%' . $letter . '%')->first();

        $correlativo_comprobante = $correlativo_['correlativo'];
        $serie_comprobante = $correlativo_['serie'];

        $data = array(

            //Cabecera del documento
            "total_gravadas"                => number_format($venta->monto, 2, '.', ''),
            "porcentaje_igv"                => "18.00",
            "total_igv"                     => "0.00",
            "total"                         => number_format($venta->monto, 2, '.', ''),
            "serie_comprobante"             => $serie_comprobante,
            "numero_comprobante"            => $correlativo_comprobante,
            "fecha_comprobante"             => date('Y-m-d'),
            "cod_tipo_documento"            => "07",
            "cod_moneda"                    => "PEN",
            "denominacion"                  => $denominacion,
            "fecha_venta"                   => $venta->fecha,
            "monto_icbper"                  => '0.00',
            "impuesto_icbper"               => '0.00',
            "tipo"                          => '1',
            "cuotas"                        => [],

            "tipo_comprobante_modifica"     => $tipo_comprobante_modifica,
            "nro_documento_modifica"        => $venta->serie_comprobante . "-" . $venta->numero_comprobante,
            "cod_tipo_motivo"               => "01",
            "descripcion_motivo"            => "Anulación de la operación",
            "tipo_proceso"                  => $enviar,
            "pass_firma"                    => (string)$claveCertificado,

            //Datos del cliente
            "cliente_numerodocumento"       => $cliente->documento,
            "cliente_nombre"                => $cliente->razon_social,
            "cliente_tipodocumento"         => $cliente_documento, //6: RUC
            "cliente_direccion"             => $cliente->dire_per,

            //data de la empresa emisora o contribuyente que entrega el documento electrónico.
            "emisor" => array(
                "ruc"                       => $ruc,
                "tipo_doc"                  => "6",
                "nom_comercial"             => $razon_social,
                "razon_social"              => $razon_social,
                "codigo_ubigeo"             => $ubigeo,
                "direccion"                 => $direccion_fiscal,
                "direccion_departamento"    => $departamento,
                "direccion_provincia"       => $provincia,
                "direccion_distrito"        => $distrito,
                "direccion_codigopais"      => "PE",
                "usuariosol"                => $usuarioSol,
                "clavesol"                  => $claveSol
            ),

            //items
            "detalle" => $dataDetalle
        );

        //Invocamos el servicio
        $token = ''; //en caso quieras utilizar algún token generado desde tu sistema
        error_reporting(0);
        //codificamos la data
        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($respuesta, true);

        $datos = array(
            "nota_credito" => $response,
            "serie" => $serie_comprobante,
            "correlativo" => $correlativo_comprobante,
            "idCorrelativo" => $correlativo_['id']
        );

        return $datos;
    }

    public function deleteNotaVenta($id)
    {
        try {
            $venta = venta::find($id);
            $venta->fecha_eliminacion = date('Y-m-d');
            $venta->user_eliminacion = session('key')->id;
            $venta->estado_nota = 2;
            $venta->save();

            $servicios = new FuncionesController;

            $detalle_venta = Detalle_venta::where('venta_id', '=', $id)->get();

            foreach ($detalle_venta as $key => $value) {
                $servicios->aumentar_descontar_stock(1, $value->ubicacion_id, $value->producto_id, $value->cantidad, $venta->tipo_envio);
                $servicios->movimiento_kardex_producto($value->ubicacion_id, $value->producto_id, $value->cantidad, 1, "ANULACION VENTA " . $venta->serie_comprobante . "-" . $venta->numero_comprobante, $venta->serie_comprobante, $venta->numero_comprobante, $value->precio, 5, date('Y-m-d'), date('Y-m-d'));
            }

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "Se elimino correctamente"
            );

            return response()->json($json);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Eliminar una venta (Factura, Boleta) - devuelve stock y registra kardex
     * Validación: Si la venta tiene crédito asociado con cuotas pendientes, no permite eliminar
     */
    public function eliminarVenta($id)
    {
        DB::beginTransaction();
        try {
            $venta = Venta::find($id);

            if (!$venta) {
                return response()->json([
                    'respuesta' => 'error',
                    'mensaje' => 'Venta no encontrada'
                ]);
            }

            // Verificar que no esté ya eliminada (por otro usuario o sesión)
            if ($venta->venta_estado == 0 || $venta->estado_nota == 2 || !empty($venta->fecha_eliminacion)) {
                return response()->json([
                    'respuesta' => 'error',
                    'mensaje' => 'Esta venta ya fue eliminada por otro usuario. Recargue la página.'
                ]);
            }

            // Validar si la venta tiene crédito asociado con cuotas pendientes
            $credito = Creditos::where('id_venta', '=', $id)
                ->where('esta_cre', '=', '1')
                ->whereNull('f_anulacion')
                ->first();

            if ($credito) {
                // Verificar si hay cuotas pendientes
                $cuotasPendientes = Cuotas::where('credito_id', '=', $credito->id)
                    ->where('esta_cuo', '=', 'PENDIENTE')
                    ->count();

                if ($cuotasPendientes > 0) {
                    DB::rollBack();
                    return response()->json([
                        'respuesta' => 'error',
                        'mensaje' => "No se puede eliminar la venta. Existe un crédito activo con $cuotasPendientes cuota(s) pendiente(s) de cobro. Primero debe cobrar o anular las cuotas pendientes."
                    ]);
                }

                // Si no hay cuotas pendientes, cancelar el crédito
                $credito->esta_cre = '2'; // Cancelado
                $credito->f_anulacion = date('Y-m-d');
                $credito->cancelacion = 'ANULACION_VENTA';
                $credito->save();
            }

            // Marcar como eliminada
            $venta->fecha_eliminacion = date('Y-m-d');
            $venta->user_eliminacion = session('key')->id;
            $venta->venta_estado = 0; // Marcar como anulada
            $venta->save();

            $servicios = new FuncionesController;

            // Restaurar stock y registrar kardex para cada producto
            $detalle_venta = Detalle_venta::where('venta_id', '=', $id)->get();

            foreach ($detalle_venta as $value) {
                // Restaurar stock (tipo=1 para aumentar)
                $servicios->aumentar_descontar_stock(1, $value->ubicacion_id, $value->producto_id, $value->cantidad, $venta->tipo_envio);

                // Registrar movimiento en kardex (tipo=1 entrada, tipo_comprobante=5 para anulación)
                $servicios->movimiento_kardex_producto(
                    $value->ubicacion_id,
                    $value->producto_id,
                    $value->cantidad,
                    1, // tipo=1 entrada
                    "ANULACION VENTA " . $venta->serie_comprobante . "-" . $venta->numero_comprobante,
                    $venta->serie_comprobante,
                    $venta->numero_comprobante,
                    $value->precio,
                    5, // tipo_comprobante=5 (anulación)
                    date('Y-m-d'),
                    date('Y-m-d')
                );
            }

            // Anular el movimiento de caja asociado si existe
            $forma_pago = Venta_formapago::where('venta_id', '=', $id)->first();
            if ($forma_pago && $forma_pago->movimiento_id) {
                DB::table('movimientos')->where('id', $forma_pago->movimiento_id)->update(['estado' => 0]);
            }

            DB::commit();

            $mensajeCredito = $credito ? ' Crédito asociado cancelado.' : '';

            return response()->json([
                'respuesta' => 'ok',
                'mensaje' => 'Venta eliminada correctamente. Stock devuelto y kardex registrado.' . $mensajeCredito
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'respuesta' => 'error',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
}
