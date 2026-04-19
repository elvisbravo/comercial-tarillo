<?php

namespace App\Http\Controllers;

use App\Amortizaciones;
use Illuminate\Http\Request;
use App\Creditos;
use App\Cuotas;
use App\Clientes;
use DB;
use Carbon\Carbon;
use App\Tipo_pago;
use App\Tipo_comprobantes;
use App\Forma_pago;
use App\Recibos;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Empresa;
use App\Http\Controllers\servicios\FuncionesController;
use App\Temamortizacion;
use App\Vendedor;
use Illuminate\Support\Facades\Log;

class AmortizacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        //



        return view('amortizacion.index');
    }

    //FUNCION TRAER LOS CLIENTES
    public function clientes()
    {

        $clientes = Clientes::where('estado_per', '=', '1')->get();
        return response()->json($clientes);
    }
    //FUNCION PARA TRAER TODOS LOS CREDITOS ACTIVOS DE UN CLIENTE SELECCIONADO

    public function creditos()
    {

        $idsede = session('key')->sede_id;

        $credito = DB::table('creditos as c')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            ->select(
                'c.id',
                'cl.id as codigo',
                'cl.razon_social',
                'cl.documento',
                'c.fpag_cre',
                'c.esta_cre',
                'c.impo_cre',
                'c.peri_cre',
                'c.periodo_pago',
                'cl.dire_per',
                'c.obse_cre',
                // Subconsulta para traer todos los productos concatenados por coma (optimizado para PostgreSQL)
                DB::raw("(SELECT STRING_AGG(p.nomb_pro, ', ') FROM detalle_venta dv JOIN productos p ON dv.producto_id = p.id WHERE dv.venta_id = c.id_venta) as productos"),
                // Subconsulta para sumar el saldo de las cuotas pendientes
                DB::raw("(SELECT COALESCE(SUM(saldo_cuo), 0) FROM cuotas WHERE credito_id = c.id AND esta_cuo = 'PENDIENTE') as saldo_pendiente")
            )
            ->where('c.esta_cre', '=', '1')
            ->where('c.sede_id', '=', $idsede)
            ->get();

        return response()->json($credito);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function cantidad_amortizada()
    {

        $amortizado = Temamortizacion::where('estado', '=', 1)->take(1)->get();



        //print_r($amortizado);exit();

        foreach ($amortizado as $a) {



            $respuesta = $this->crear($a->id);


            // print_r($respuesta);
            //print_r('');exit();
        }

        print_r('ok');
        exit();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //Request $request
    public function crear(Request $request)
    {
        //
        DB::beginTransaction();

        try {

            /* $amortizado=Temamortizacion::find($id);
            $amortizado->estado=0;
            $amortizado->save();*/

            // return $amortizado->monto;



            $user = Auth::user();




            $respuesta = '';
            /* $recibo=Recibos::orderBy('id','asc')->get();
            $ultimo=$recibo->last();
            $variablecodigo = 0;

                     Log::info('=== GUARDANDO AMORTIZACION ===');
            if($ultimo==""){
                $variablecodigo = 0;


            }else{                $variablecodigo = (int)$ultimo->num_recibo;
            }


            $result = array();
            $result=$this->ultimorecibo($variablecodigo+1,1,8);*/

            $ultimo = Recibos::orderBy('id', 'desc')->first();
            $variablecodigo = is_null($ultimo) ? 0 : (int)$ultimo->num_recibo;
            $result = $this->ultimorecibo($variablecodigo + 1, 1, 8);

            $tipo = 0;


            $parametros_movimiento = [
                'tipo' => "INGRESO",
                'fecha' => $request->fpag_rec,
                'param3' => 1,
                'monto' => $request->mont_rec,
                'descripcion' => "PAGO DE CUOTAS DE CREDITO",
                'param6' => 1,
                'param7' => 1,
                'num_recibo' => $result[0],
                'param9' => 1,
            ];

            // Log para depuración
            Log::info('Parámetros para generar_movimiento', $parametros_movimiento);
            $idsede = session('key')->sede_id;

            $recibo = new Recibos;
            $recibo->mont_rec = $request->mont_rec; //$amortizado->monto;// 
            $recibo->fech_rec = $request->fech_rec; //Date('Y-m-d');/// 
            $recibo->cliente_id = $request->cliente_id; //$amortizado->cliente_id; // 
            $recibo->fpag_rec = $request->fpag_rec; //$amortizado->fecha;//
            $recibo->obse_rec = $request->obse_rec;
            $recibo->vendedor_id = $request->vendedor_id;
            $recibo->esta_rec = 'EMITIDO';
            $recibo->docu_ref = $request->docu_ref;
            $recibo->insercion = 'MANUAL';
            $recibo->usuario = $user->name;
            $recibo->num_recibo = $result[0];
            $serviciomovimiento = new FuncionesController;
            $recibo->id_movimiento = $serviciomovimiento->generar_movimiento("INGRESO", $request->fpag_rec, 1, $request->mont_rec, "PAGO DE CUOTAS DE CREDITO", 1, 1, $result[0], 1);
            //$recibo->id_movimiento=$serviciomovimiento->generar_movimiento("INGRESO",$amortizado->fecha,1,$amortizado->monto,"PAGO DE CUOTA",1,1,$result[0],1);
            $recibo->sede_id = $idsede;
            Log::info('Contenido del Recibo antes de guardar', $recibo->toArray());
            $recibo->save();

            //return response()->json($recibo);

            //print_r('');exit();

            Log::info('Paso 3: Recibo guardado correctamente', ['recibo_id' => $recibo->id]);
            $cuotas = $this->total_cuota($request->credito_id);
            //$cuotas=$this->total_cuota( $amortizado->credito_id);
            $saldo = $request->mont_rec; //$amortizado->monto;//
            $total = 0;

            Log::info('Paso 4: Cuotas pendientes obtenidas', ['total_cuotas' => count($cuotas)]);

            for ($i = 0; $i < count($cuotas); $i++) {

                $amortizacion = new Amortizaciones;

                if ($i == 0) {

                    $total = $this->amortizar($cuotas[$i]->id, $saldo);

                    $amortizacion->mont_amo = $saldo - $total;
                    $amortizacion->fech_amo = Date('Y-m-d');
                    $amortizacion->cuota_id = $cuotas[$i]->id;
                    $amortizacion->recibo_id = $recibo->id;

                    if (($cuotas[$i]->saldo_cuo - ($saldo - $total)) == 0) {

                        $amortizacion->tipo_amo = 'TOTAL';
                    } else {
                        $amortizacion->tipo_amo = 'PARCIAL';
                    }


                    $amortizacion->capi_amo = $saldo - $total;
                    $amortizacion->inte_amo = 0.00;
                    $amortizacion->saldo_cuo = ($cuotas[$i]->saldo_cuo - ($saldo - $total));
                    $amortizacion->save();
                } else {


                    if ($total == 0) {
                        break;
                    } else {

                        $temporal = $total;
                        $total = $this->amortizar($cuotas[$i]->id, $total);
                        $monto = $saldo - $total;

                        $amortizacion->mont_amo = $temporal - $total;
                        $amortizacion->fech_amo = Date('Y-m-d');
                        $amortizacion->cuota_id = $cuotas[$i]->id;
                        $amortizacion->recibo_id = $recibo->id;
                        $amortizacion->capi_amo = $temporal - $total;
                        $amortizacion->inte_amo = 0.00;
                        $amortizacion->saldo_cuo = ($cuotas[$i]->saldo_cuo - ($temporal - $total));

                        if (($cuotas[$i]->saldo_cuo - ($temporal - $total)) == 0) {

                            $amortizacion->tipo_amo = 'TOTAL';
                        } else {

                            $amortizacion->tipo_amo = 'PARCIAL';
                        }

                        $amortizacion->save();


                        if ($saldo == $monto) {

                            break;
                        } else {

                            // return response()->json($total);
                            //print_r('');exit();


                            $total = $this->amortizar($cuotas[$i]->id, $total);
                        }
                    }
                }
            }

            //METODO PARA CAMBIAR EL ESTADO DEL CREDITO $amortizado->credito_id

            $saldo_capital = $this->saldo_credito($request->credito_id);
            Log::info('Paso 5: Saldo total del crédito', ['saldo_capital' => $saldo_capital]);

            $this->estado_credito($request->credito_id, $saldo_capital);


            Log::info('=== AMORTIZACION GUARDADA CORRECTAMENTE ===', [
                // 'id' => $amortizacion->id
            ]);

            DB::commit();
            // return response()->json($respuesta);
            return response()->json('OK');
        } catch (Exception $e) {

            Log::error('Error: ' . $e->getMessage());

            return  response()->json($e);
        }
    }

    //METODO PARA DEVOLVER TODAS LAS CUOTAS PENDIENTES Y REPROGRAMADAS
    public function total_cuota($id_credito)
    {

        $cuotas = Cuotas::where('credito_id', '=', $id_credito)
            //->whereIn('esta_cuo', ['PENDIENTE','REPROGRAMADA'])
            ->whereIn('esta_cuo', ['PENDIENTE'])
            ->orderBy('numero_cuo', 'asc')
            ->get();

        return $cuotas;
    }

    //metod para amorizar la cuota
    public function amortizar($id_cuota, $monto)
    {

        Log::info('=== INICIO MÉTODO AMORTIZAR ===', [
            'id_cuota' => $id_cuota,
            'monto_recibido' => $monto
        ]);

        $monto_des = 0;

        $cuota = Cuotas::where('id', '=', $id_cuota)->first();

        Log::info('Cuota encontrada', [
            'id' => $cuota->id ?? null,
            'saldo_capital' => $cuota->sald_cap ?? null,
            'saldo_cuota' => $cuota->saldo_cuo ?? null,
            'estado_actual' => $cuota->esta_cuo ?? null
        ]);


        if (($monto) - ($cuota->sald_cap) < 0) {

            $cuota->sald_cap = (-1) * (($monto) -  ($cuota->sald_cap));
            $cuota->saldo_cuo = (-1) * (($monto) -  ($cuota->saldo_cuo));
            $cuota->esta_cuo = 'PENDIENTE';
            $monto_des = 0;
        } else if (($monto) -  ($cuota->sald_cap) > 0) {

            $monto_des = ($monto) -  ($cuota->sald_cap);
            $cuota->sald_cap = 0.00;
            $cuota->saldo_cuo = 0.00;
            $cuota->esta_cuo = 'COBRADA';
        } else if (($monto) - ($cuota->sald_cap) == 0) {

            $cuota->sald_cap = (1) * (($monto) -  ($cuota->sald_cap));
            $cuota->saldo_cuo = (1) * (($monto) -  ($cuota->saldo_cuo));
            $cuota->esta_cuo = 'COBRADA';
            $monto_des = 0;
        }

        $cuota->save();


        return $monto_des;
    }

    //TOTAL SALDO CAPITAL
    public function saldo_credito($id_credito)
    {

        $saldo = Cuotas::where('credito_id', '=', $id_credito)
            ->select(DB::raw('SUM(sald_cap) as saldo'))
            ->get();

        return $saldo[0]->saldo;
    }

    //METOD PARA CAMBIAR EL ESTADO DEL CREDITO
    public function estado_credito($id_credito, $monto)
    {

        $credito = Creditos::where('id', '=', $id_credito)->first();
        if ($monto == 0) {

            $credito->esta_cre = '2';
            $credito->save();
        }
    }

    //FUNCION PARA DEVOLVER EL ULTIMO VALOR AGREGADO A LA TABLA
    public function ultimorecibo($start, $count, $digits)
    {

        //TRAER EL ULTIMO ID GENERADO PARA EL CORRELATIVO
        //$recibox=Recibo::all();
        //$ultimo=$recibox->last();
        $result = array();
        for ($n = $start; $n < $start + $count; $n++) {

            $result[] = str_pad($n, $digits, "0", STR_PAD_LEFT);
        }

        return $result;
    }





    /**
     * Display the specified resource.
     *
     * @param  \App\Amortizaciones  $amortizaciones
     * @return \Illuminate\Http\Response
     */
    public function show(Amortizaciones $amortizaciones)
    {
        //
    }

    public function detalle_product($id)
    {

        $credito = DB::table('creditos as c')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            //->join('cuotas as c','c.credito_id','=','cre.id')
            ->select(
                'c.id',
                'cl.id as codigo',
                'cl.razon_social',
                'cl.documento',
                'c.fpag_cre',
                'c.esta_cre',
                'c.impo_cre',
                'c.peri_cre',
                'c.periodo_pago',
                'cl.dire_per',
                'c.obse_cre',
                'id_venta'
            )
            //->where('c.esta_cre','=','1')
            ->where('c.id', '=', $id)
            ->first();

        $detalle = DB::table('detalle_venta')
            ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
            ->where('detalle_venta.venta_id', '=', $credito->id_venta)
            ->get();

        return response()->json($detalle);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Amortizaciones  $amortizaciones
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $formapago = Forma_pago::all();
        $comprobante = Tipo_comprobantes::all();
        $fecha = Date('Y-m-d');



        $credito = DB::table('creditos as c')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            //->join('cuotas as c','c.credito_id','=','cre.id')
            ->select(
                'c.id',
                'cl.id as codigo',
                'cl.razon_social',
                'cl.documento',
                'c.fpag_cre',
                'c.esta_cre',
                'c.impo_cre',
                'c.peri_cre',
                'c.periodo_pago',
                'cl.dire_per',
                'c.obse_cre',
                'id_venta'
            )
            //->where('c.esta_cre','=','1')
            ->where('c.id', '=', $id)
            ->first();

        $detalle = DB::table('detalle_venta')
            ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
            ->where('detalle_venta.venta_id', '=', $credito->id_venta)
            ->get();

        //print_r($detalle);exit();

        $cuotas = DB::table('creditos as cre')
            ->join('cuotas as c', 'c.credito_id', '=', 'cre.id')
            ->select('c.id', 'c.credito_id', 'c.numero_cuo', 'c.mont_cuo', 'c.capi_cuo', 'c.fven_cuo', 'c.esta_cuo', 'c.saldo_cuo')
            //->where('cre.esta_cre','=','1')
            ->where('cre.id', '=', $id)
            //->where('c.esta_cuo','=','PENDIENTE')
            ->orderBy('c.numero_cuo', 'asc')
            ->get();

        //VENDEDORES
        $vendedores = Vendedor::all();

        $amortizaciones_realizadas = DB::table('amortizaciones as a')
            ->join('cuotas as c', 'a.cuota_id', '=', 'c.id')
            ->join('recibos as r', 'a.recibo_id', '=', 'r.id')
            ->join('movimientos as m', 'r.id_movimiento', '=', 'm.id')
            ->join('forma_pagos as fp', 'm.forma_pago_id', '=', 'fp.id')
            ->select(
                'a.created_at',
                'r.num_recibo',
                'fp.descripcion as forma_pago',
                DB::raw('SUM(a.mont_amo) as monto')
            )
            ->where('c.credito_id', '=', $id)
            ->where('r.esta_rec', '!=', 'ANULADO')
            ->groupBy('a.created_at', 'r.num_recibo', 'fp.descripcion')
            ->orderBy('a.created_at', 'desc')
            ->get();

        return view('amortizacion.edit', compact('credito', 'cuotas', 'formapago', 'comprobante', 'fecha', 'vendedores', 'detalle', 'amortizaciones_realizadas'));
    }

    //TRAER EL ULTIMO PAGO PARA IMPRIMIR RECIBO

    public function recibo()
    {

        $empresa = Empresa::first();
        $recibo = Recibos::orderBy('id', 'asc')->get();
        $ultimo = $recibo->last();
        $cliente = Clientes::where('id', '=', $ultimo->cliente_id)->first();

        $vendedor = Vendedor::where('id', '=', $ultimo->vendedor_id)->first();

        //$amortizacion=Amortizaciones::where('recibo_id','=',$ultimo->id)->get();
        $amortizacion = DB::table('amortizaciones as a')
            ->join('cuotas as c', 'a.cuota_id', '=', 'c.id')
            ->where('recibo_id', '=', $ultimo->id)
            ->get();

        $saldo = Cuotas::where('credito_id', '=', $amortizacion[0]->credito_id)
            ->select(DB::raw('SUM(sald_cap) as saldo'), 'credito_id')
            ->groupBy('credito_id')
            ->get();

        //detalle de venta
        $credito = DB::table('creditos as c')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            //->join('cuotas as c','c.credito_id','=','cre.id')
            ->select(
                'c.id',
                'cl.id as codigo',
                'cl.razon_social',
                'cl.documento',
                'c.fpag_cre',
                'c.esta_cre',
                'c.impo_cre',
                'c.peri_cre',
                'c.periodo_pago',
                'cl.dire_per',
                'c.obse_cre',
                'id_venta'
            )
            //->where('c.esta_cre','=','1')
            ->where('c.id', '=', $saldo[0]->credito_id)
            ->first();

        $detalle = DB::table('detalle_venta')
            ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
            ->where('detalle_venta.venta_id', '=', $credito->id_venta)
            ->get();


        $date_proximo = $this->cuota_activa($amortizacion[0]->credito_id);


        //print_r($date_proximo->fven_cuo);exit();

        $pdf = new Fpdf('P', 'mm', array(80, 220));
        $pdf->AddPage();

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Image('./img/logo2.jpeg', 20, 5, 40, 20);
        $pdf->Ln(15);

        //$pdf->MultiCell(60,4,$empresa['razon_social'],0,'C');
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->MultiCell(60, 4, $empresa['nombre_comercial'], 0, 'C');

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->MultiCell(60, 4, utf8_decode($empresa['direccion_fiscal']), 0, 'C');


        $pdf->MultiCell(60, 4, "RUC: " . $empresa['ruc'], 0, 'C');
        $pdf->Ln(1);


        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(60, 4, utf8_decode('RECIBO DE COBRANZA'), 0, 'C');
        $pdf->MultiCell(60, 4, utf8_decode('N° : ' . $ultimo->num_recibo), 0, 'C');

        if (isset($detalle[0]->descripcion)) {
            $pdf->MultiCell(60, 4, utf8_decode('PRODUCTO: ' . $detalle[0]->descripcion), 0, 'C');
        } else {
            $pdf->MultiCell(60, 4, utf8_decode('PRODUCTO: ' . $credito->obse_cre), 0, 'C');
        }

        $pdf->SetFont('Helvetica', '', 8);

        $pdf->Ln(5);
        $pdf->MultiCell(60, 4, 'CLIENTE: ' . utf8_decode($cliente->razon_social), 0, '');
        $pdf->MultiCell(60, 4, 'DNI/RUC: ' . $cliente->documento, 0, '');
        $pdf->MultiCell(60, 4, utf8_decode('DIRECCIÓN: ' . $cliente->dire_per), 0, '');
        $pdf->MultiCell(60, 4, 'FECHA: ' . date('d-m-Y', strtotime($ultimo->fech_rec)), 0, '');
        //$pdf->MultiCell(60,4,'COBRADOR: '.$ultimo->usuario,0,'');


        $pdf->Ln(1);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(60, 4, utf8_decode('DETALLE DE LA AMORTIZACION'), 0, 'C');
        $pdf->SetFont('Helvetica', '', 8);

        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->Cell(28, 10, utf8_decode('N° Cuota'), 0);
        $pdf->Cell(5, 10, 'Monto Cuota', 0, 0, 'R');
        $pdf->Cell(17, 10, 'Amortizado', 0, 0, 'R');
        $pdf->Cell(10, 10, 'Saldo', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(60, 0, '', 'T');
        $pdf->Ln(2);

        // PRODUCTOS
        $pdf->SetFont('Helvetica', '', 8);

        // PRODUCTOS
        $pdf->SetFont('Helvetica', '', 8);

        $total_venta = 0;

        foreach ($amortizacion as $key => $value) {

            //$total_venta += $value->cantidad * $value->precio;

            $pdf->MultiCell(5, 4, '', 0, 'L');
            $pdf->Cell(15, -5, $value->numero_cuo);
            $pdf->Cell(20, -5, number_format($value->mont_cuo, 2, ',', ' '));
            $pdf->Cell(15, -5, number_format($value->mont_amo, 2, ',', ' '));
            $pdf->Cell(15, -5, number_format($value->saldo_cuo, 2, ',', ' '));
            $pdf->Ln(2);
        }



        $pdf->Cell(60, 0, '', 'T');
        $pdf->Ln(2);

        $pdf->Ln(1);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(60, 4, utf8_decode('TOTAL DE COBRANZA:  ') . number_format($ultimo->mont_rec, 2, ',', ' '), 0, 'C');
        $pdf->SetFont('Helvetica', '', 8);


        $pdf->Ln(5);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(60, 4, utf8_decode('SALDO PENDIENTE:  ') . number_format($saldo[0]->saldo, 2, ',', ' '), 0, 'C');
        $pdf->SetFont('Helvetica', '', 8);

        //PONER LA FECHA PROXIMA PENDIENTE
        if (isset($date_proximo->fven_cuo)) {
            $pdf->Ln(5);
            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->MultiCell(60, 4, utf8_decode('PROXIMA FECHA DE PAGO:  ') . $date_proximo->fven_cuo, 0, 'C');
            $pdf->SetFont('Helvetica', '', 8);
        }





        $pdf->Ln(10);
        //$pdf->MultiCell(60,4,'SON: '.$letras,0,'');

        $pdf->MultiCell(60, 4, utf8_decode('COBRADOR: ' . $vendedor->nombre), 0, '');
        $pdf->Ln(15);
        $pdf->Cell(60, 0, '', 'T');
        $pdf->Ln(1);
        $pdf->MultiCell(150, 4, 'FIRMA DEL CAJERO(A): ', 0, '');


        ob_get_clean();
        $pdf->Output('recibo.pdf', 'I');


        //PROMEDIO = DIVIDE(SUM(CUBO[SOLES]),DISTINCTCOUNT(CUBO[nropedido]))





    }

    //TRAER LA CUOTA PEDIENTE
    public function cuota_activa($codigo)
    {

        $cuotas = DB::table('cuotas as c')
            ->where('c.credito_id', '=', $codigo)
            ->where('c.esta_cuo', '=', 'PENDIENTE')
            ->orderBy('c.fven_cuo', 'asc')
            ->first();

        return $cuotas;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Amortizaciones  $amortizaciones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Amortizaciones $amortizaciones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Amortizaciones  $amortizaciones
     * @return \Illuminate\Http\Response
     */
    public function destroy(Amortizaciones $amortizaciones)
    {
        //
    }

    // FUNCTION TO CANCEL AN AMORTIZATION DIRECTLY FROM EDIT VIEW
    public function anular_recibo_amort(Request $request)
    {
        DB::beginTransaction();

        try {
            $num_recibo = $request->num_recibo;
            $eliminar_movimiento = $request->eliminar_movimiento;

            $recibos = Recibos::where('num_recibo', '=', $num_recibo)->get();

            if ($recibos->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Recibo no encontrado']);
            }

            $user = Auth::user();
            $idsede = session('key')->sede_id;

            foreach ($recibos as $recibo) {
                // Anular recibo
                $recibo->esta_rec = 'ANULADO';
                $recibo->f_anulacion = date('Y-m-d');
                $recibo->obse_rec = "Anulado desde edición. Fecha: " . date('Y-m-d') . " Usuario: " . $user->name . " Sede: " . $idsede;
                $recibo->usuario_anulo = $user->name;
                $recibo->mont_rec = (-1) * abs($recibo->mont_rec);
                $recibo->save();

                // Revertir amortizaciones asociadas al recibo
                $amortizaciones = Amortizaciones::where('recibo_id', '=', $recibo->id)->get();
                foreach ($amortizaciones as $de) {
                    $cuota = Cuotas::where('id', '=', $de->cuota_id)->first();
                    $validar_cuota = $cuota->mont_cuo - $de->mont_amo;

                    if ($validar_cuota == 0) {
                        $cuota->saldo_cuo = $de->mont_amo;
                        $cuota->sald_cap = $de->mont_amo;
                    } else {
                        $cuota->saldo_cuo = $cuota->saldo_cuo + $de->mont_amo;
                        $cuota->sald_cap = $cuota->sald_cap + $de->mont_amo;
                    }

                    $cuota->esta_cuo = 'PENDIENTE';
                    $cuota->save();

                    // Reabrir el estado del crédito si estaba cobrado (2)
                    $credito = Creditos::where('id', '=', $cuota->credito_id)->first();
                    if ($credito->esta_cre == '2') {
                        $credito->esta_cre = '1';
                        $credito->save();
                    }
                }

                // Inactivar movimiento de caja si el checkbox lo indicó (1)
                $eliminar_movimiento = filter_var($eliminar_movimiento, FILTER_VALIDATE_BOOLEAN);
                if ($eliminar_movimiento) {
                    if (!empty($recibo->id_movimiento)) {
                        DB::table('movimientos')->where('id', $recibo->id_movimiento)->update(['estado' => 0]);
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error anulando amortización: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
