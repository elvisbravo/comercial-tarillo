<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Productos;
use App\Tipo_documento;
use App\Tipo_comprobantes;
use App\Detalle_venta;
use App\Venta;
use App\Clientes;
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
use App\Sector;
use App\Sede;
use Spatie\Permission\Models\Role;
use Codedge\Fpdf\Fpdf\Fpdf;

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


    public function index(Request $request)
    {
        $servicios = new FuncionesController;

        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $envio = $servicios->tipo_envio_sunat();

        $ventas = DB::table('ventas')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->join('tipo_comprobantes', 'ventas.tipo_comprobante_id', '=', 'tipo_comprobantes.id')
            ->select('clientes.nomb_per', 'clientes.pate_per', 'clientes.mate_per', 'clientes.documento', 'tipo_comprobantes.descripcion as comprobante', 'ventas.id', DB::raw("to_char(ventas.fecha, 'DD-MM-YYYY') as fecha"), 'ventas.hora', 'ventas.serie_comprobante', 'ventas.numero_comprobante', 'ventas.monto', 'ventas.sede_id', 'ventas.venta_estado', 'ventas.aceptado_sunat', 'ventas.mensaje_sunat', 'ventas.tipo_comprobante_id', 'ventas.estado_nota', 'ventas.serie_nota_credito', 'ventas.numero_nota_credito')
            ->where('ventas.tipo_envio', '=', $envio)
            ->where('ventas.sede_id', '=', $idsede)
            ->orderBy('ventas.id', 'desc')
            ->paginate(10);

        return view('ventas.index', compact('ventas'))->with('i', ($request->input('page', 1) - 1) * 10);
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
            ->get();

        $userId = session('key')->id;

        $vendedores = Vendedor::where('estado', '=', 1)->get();

        $sectores = Sector::where('estado', '=', 'ACTIVO')->get();

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
            ->where('p.nomb_pro', 'like', '%' . $buscar . '%')
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
                $cliente->id_sector = $post['sectores'];

                $cliente->save();

                $id_cliente = $cliente->id;
            } else {
                $id_cliente = $consulta_cliente->id;

                $cliente_ = Clientes::find($id_cliente);

                $cliente_->telefono = $post['celular_cliente'];
                $cliente_->dire_per = $post['direccion_cliente'];
                $cliente_->razon_social = $post['nombre_cliente'];
                $cliente_->id_sector = $post['sectores'];

                $cliente_->save();
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
            $venta->vendedor_id = $post['vendedor'];

            $venta->save();

            $id_venta = $venta->id;

            $cantidades = $post['quanty'];
            $productos = $post['idproducto'];
            $precios = $post['priceproducto'];
            $descripcion = $post['nameproducto'];
            $subtotal = $post['importe'];
            $ubicaciones = $post['ubicacion'];

            $hasta = count($cantidades);

            for ($i = 0; $i < $hasta; $i++) {
                $detalle = new Detalle_venta;

                $detalle->producto_id = $productos[$i];
                $detalle->venta_id = $id_venta;
                $detalle->cantidad = $cantidades[$i];
                $detalle->precio = $precios[$i];
                $detalle->subtotal = $subtotal[$i];
                $detalle->descripcion = $descripcion[$i];
                $detalle->ubicacion_id = $ubicaciones[$i];

                $detalle->save();

                if ($tipo_comprobante != 9) {
                    $servicios->aumentar_descontar_stock(0, $ubicaciones[$i], $productos[$i], $cantidades[$i], $envio);
                    $servicios->movimiento_kardex_producto($ubicaciones[$i], $productos[$i], $cantidades[$i], 2, "VENTA", $serie, $numero, $precios[$i], $tipo_comprobante, date('Y-m-d'), date('Y-m-d'));
                }
            }

            if ($post['tipo_venta'] == 1) {
                $estado_mov = 1;
            } else {
                $estado_mov = 0;
            }

            if ($post['forma_pago'] != 9) {
                $forma_venta = new Venta_formapago;

                $movimiento_id = $servicios->generar_movimiento("INGRESO", $post['forma_pago'], 9, $post['total_venta'], "VENTA DE MERCADERIA", $tipo_comprobante, 1, $serie . "-" . $numero, $estado_mov);

                $forma_venta->venta_id = $id_venta;
                $forma_venta->forma_pago_id = $post['forma_pago'];
                $forma_venta->monto = $post['total_venta'];
                $forma_venta->numero_operacion = $post['numero_operacion'];
                $forma_venta->banco_id = $post['banco_venta'];
                $forma_venta->movimiento_id = $movimiento_id;

                $forma_venta->save();
            } else {
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
                    $forma_venta->numero_operacion = $formaNumero[$i];
                    $forma_venta->banco_id = $formaBanco[$i];
                    $forma_venta->movimiento_id = $movimiento_id;
                    $forma_venta->save();
                }
            }

            $json = array(
                "id" => $id_venta,
                "mensaje" =>  "Se registro correctamente la venta",
                "respuesta" => "ok"
            );

            DB::commit();

            return response()->json($json);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);
        }
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
                $servicios->movimiento_kardex_producto($value->ubicacion_id, $value->producto_id, $value->cantidad, 1, "NOTA DE VENTA", $venta->serie_comprobante, $venta->numero_comprobante, $value->precio, 5, date('Y-m-d'), date('Y-m-d'));
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
