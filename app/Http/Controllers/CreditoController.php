<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Creditos;
use App\Venta;
use App\Cuotas;
use DB;
use App\candado;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Clientes;
use App\Empresa;
use PDF;
use App\User;
use App\Concepto_credito;

class CreditoController extends Controller
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
        $conceptos=Concepto_credito::where('estado','=',1)->get();

        return view('creditos.index',compact('conceptos'));
    }

    public function ventas_credito(Request $request){


        $ventas=DB::table('ventas as v')
        ->join('clientes as c','v.cliente_id','=','c.id')
        ->select('v.id','c.id as codigo','c.documento','c.razon_social','v.fecha','v.serie_comprobante','v.numero_comprobante','v.monto','v.tipo_pago_id')
        ->where('v.tipo_pago_id','=',2)
        ->where('v.venta_estado','=',1)
        ->where('v.estado_nota','=',1)
        ->get();


        return response()->json($ventas);


    }



    //FUNCION PARA VERIFICAR LOS CANDADOS DE ACUERDO A LOS MONTOS
     public function validador_candados($monto){

         /*$candados=candado::where('estado','=',1)
         ->select('rango_minimo','rango_maximo','monto_inicial','nmeses')
         ->get();
         $datos=array();
         //$monto=0;
          foreach($candados as $c){

              if($monto <=$c->rango_maximo ){
                 //$datos=$c->nmeses.'-'.$c->monto_inicial;
                 array_push($datos,$c->nmeses,$c->monto_inicial);
                 break;
              }

          }

         return response()->json($datos);*/





     }

     //METODO PARA SABER SI UN CLIENTE TIENE DEUDA
      public function deudaantigua($id){

          $creditos=DB::table('creditos as cre')
          ->join('cuotas as c','cre.id','=','c.credito_id')
          ->select(DB::raw('SUM(c.saldo_cuo) as monto'))
          ->where('cre.esta_cre','=','1')
          ->where('cre.cliente_id','=',$id)
          ->get();
          $total=0;

          if( $creditos[0]->monto==""){
              $total=0;
          }else{
              $total=$creditos[0]->monto;
          }

          return response()->json($total);

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function crear(Request $request)
    {
        //

        DB::beginTransaction();

        try{

            $resultado=$request->all();
            $user = Auth::user();

            $idsede = session('key')->sede_id;

            $creditos=new Creditos;
            $creditos->mont_cre=$resultado[0]["mont_cre"];
            $creditos->esta_cre='1';
            $creditos->fech_cre=Date('Y-m-d');
            $creditos->inte_cre=$resultado[0]["inte_cre"];
            $creditos->impo_cre=$resultado[0]["mont_cre"];
            $creditos->fpag_cre=$resultado[0]["fpag_cre"];
            $creditos->peri_cre=$resultado[0]["peri_cre"];
            $creditos->cliente_id=$resultado[0]["cliente_id"];
            $creditos->obse_cre=$resultado[0]["obse_cre"];
            $creditos->usuario=$user->name;
            $creditos->tipo_doc=$resultado[0]["tipo_doc"];
            $creditos->id_venta=$resultado[0]["id_venta"];
            $creditos->periodo_pago=$resultado[0]["periodo_pago"];
            $creditos->sede_id=$idsede;
            $creditos->id_con=$resultado[0]["id_con"];
            $creditos->save();

            //METODO PARA MODIFICAR EL ESTADO DE LA VENTA
            $venta=Venta::where('id',$resultado[0]["id_venta"])->first();
            $venta->venta_estado=2;
            $venta->save();




            for($i=1; $i <count($resultado) ; $i++){


                $cuota=new Cuotas;
                $cuota->mont_cuo=$resultado[$i]["mont_cuo"];
                $cuota->fven_cuo=Carbon::parse($resultado[$i]["fven_cuo"])->format('Y-m-d');
                $cuota->saldo_cuo=$resultado[$i]["mont_cuo"];
                $cuota->capi_cuo=$resultado[$i]["mont_cuo"];
                $cuota->credito_id= $creditos->id;
                $cuota->esta_cuo= 'PENDIENTE';
                $cuota->numero_cuo= $resultado[$i]["numero_cuo"];
                $cuota->sald_cap= $resultado[$i]["mont_cuo"];
                $cuota->version=1;
                $cuota->save();



            }

            DB::commit();


            return response()->json($creditos->id);



        }catch (Exception $e) {

            return  response()->json($e);

        }
    }
    //FUNCION PARA IMPRIRMIR CONTRATO
    public function contrato($codigo){

        $users = User::get();
        $credito=DB::table('creditos as c')
          ->join('clientes as cl','c.cliente_id','=','cl.id')
          //->join('cuotas as c','c.credito_id','=','cre.id')
          ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
            'c.periodo_pago','cl.dire_per','c.peri_cre','c.id_venta')
          ->where('c.esta_cre','=','1')
          ->where('c.id','=',$codigo)
          ->first();


          $detalle = DB::table('detalle_venta')
          ->join('productos','detalle_venta.producto_id','=','productos.id')
          ->where('detalle_venta.venta_id','=',$credito->id_venta)
          ->get();



         // print_r($detalle);exit();

        $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('m/d/Y'),
            'users' => $users
        ];

        $pdf = PDF::loadView('contrato', compact('data','credito','detalle'));


        return $pdf->stream('contrato.pdf');


    }

    public function cuotas(){

          $empresa = Empresa::first();

          $creditos=Creditos::orderBy('id','asc')->get();
          $ultimo=$creditos->last();
          $cliente=Clientes::where('id','=',$ultimo->cliente_id)->first();

          $venta = DB::table('ventas')
          ->join('clientes','ventas.cliente_id','=','clientes.id')
          ->join('tipo_comprobantes','ventas.tipo_comprobante_id','=','tipo_comprobantes.id')
          ->where('ventas.id','=',$ultimo->id_venta)
          ->first();

          $detalle = DB::table('detalle_venta')
          ->join('productos','detalle_venta.producto_id','=','productos.id')
          ->where('detalle_venta.venta_id','=',$ultimo->id_venta)
          ->get();

          $cuota=Cuotas::where('credito_id','=',$ultimo->id)->get();

          $pdf = new Fpdf('P','mm',array(80,297));
          $pdf->AddPage();

          $pdf->SetFont('Helvetica','',8);
          $pdf->Image('./img/logo2.jpeg',20,5,40,20);
          $pdf->Ln(15);

          //$pdf->MultiCell(60,4,$empresa['razon_social'],0,'C');
          $pdf->SetFont('Helvetica','B',11);
          $pdf->MultiCell(60,4,$empresa['nombre_comercial'],0,'C');

          $pdf->SetFont('Helvetica','',8);
          $pdf->MultiCell(60,4,utf8_decode($empresa['direccion_fiscal']),0,'C');


          $pdf->MultiCell(60,4,"RUC: ".$empresa['ruc'],0,'C');
          $pdf->Ln(1);

          $pdf->SetFont('Helvetica', 'B', 8);
          $pdf->MultiCell(60,4,utf8_decode('INFORMACIÓN DEL CREDITO'),0,'C');
          $pdf->MultiCell(60,4,utf8_decode('N° Credito: '.$ultimo->id),0,'C');
          $pdf->MultiCell(60,4,utf8_decode('Concepto: POR LA VENTA DE MERCADERIA'),0,'C');
          $pdf->SetFont('Helvetica','',8);

          $pdf->Ln(5);
          $pdf->MultiCell(60,4,'CLIENTE: '.utf8_decode($venta->razon_social),0,'');
          $pdf->MultiCell(60,4,'CONYUGUE: '.utf8_decode($venta->conyugue),0,'');
          $pdf->MultiCell(60,4,'DNI/RUC: '.$venta->documento,0,'');
          $pdf->MultiCell(60,4,'TELEFONO: '.$cliente->telefono,0,'');
          $pdf->MultiCell(60,4,utf8_decode('DIRECCIÓN: '.$venta->dire_per),0,'');
          $pdf->MultiCell(60,4,utf8_decode('REFERENCIA: '.$venta->referencia),0,'');
          $pdf->MultiCell(60,4,'PERIODO DE PAGO: '.utf8_decode($ultimo->periodo_pago),0,'');
          $pdf->MultiCell(60,4,'FECHA: '.date('d-m-Y',strtotime($venta->fecha)),0,'');

          $pdf->Ln(1);
          $pdf->SetFont('Helvetica', 'B', 8);
          $pdf->MultiCell(60,4,utf8_decode('DETALLE DEL PRODUCTO OTORGADO'),0,'C');
          $pdf->SetFont('Helvetica','',8);

          $pdf->SetFont('Helvetica', 'B', 8);
          $pdf->Cell(28, 10, utf8_decode('Descripción'), 0);
          $pdf->Cell(5, 10, 'Und',0,0,'R');
          $pdf->Cell(10, 10, 'Precio',0,0,'R');
          $pdf->Cell(15, 10, 'Total',0,0,'R');
          $pdf->Ln(8);
          $pdf->Cell(60,0,'','T');
          $pdf->Ln(2);

          // PRODUCTOS
         $pdf->SetFont('Helvetica', '', 8);

         $total_venta = 0;

        foreach ($detalle as $key => $value) {

            $total_venta += $value->cantidad * $value->precio;

            $pdf->MultiCell(30,4,utf8_decode($value->nomb_pro),0,'L');
            $pdf->Cell(32, -5, $value->cantidad,0,0,'R');
            $pdf->Cell(15, -5, number_format($value->precio, 2, '.', ' '));
            $pdf->Cell(15, -5, "S/ ".number_format($value->precio * $value->cantidad, 2, '.', ' '));
            $pdf->Ln(2);
        }



        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);

        $pdf->Ln(1);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(60,4,utf8_decode('CRONOGRAMA DE PAGOS'),0,'C');
        $pdf->SetFont('Helvetica','',8);

          $pdf->Ln(1);
          $pdf->SetFont('Helvetica', 'B', 8);
          $pdf->MultiCell(60,4,'CUOTA INICIAL: '.$cuota[0]->mont_cuo,0,'');



        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->Cell(18, 5, utf8_decode('N°'), 0);
        $pdf->Cell(-2, 5, 'Cuota',0,0,'R');
       // $pdf->Cell(10, 5, 'Interes',0,0,'R');
        $pdf->Cell(10, 5, 'Saldo',0,0,'R');
        $pdf->Cell(20, 5, 'Vencimiento',0,0,'R');
        $pdf->Cell(12, 5, 'Estado',0,0,'R');
        $pdf->Ln(8);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);

        $pdf->SetFont('Helvetica', '', 8);

         $total_cuota=0;
         $total_saldo=0;

        foreach ($cuota as $key => $value) {

            $total_cuota += $value->mont_cuo;
            $total_saldo += $value->saldo_cuo;

            $pdf->MultiCell(1,4,'',0,'L');
            $pdf->Cell(5, -5, $value->numero_cuo,0,0,'R');
            $pdf->Cell(10, -5, $value->mont_cuo,0,0,'R');
            $pdf->Cell(10, -5, $value->saldo_cuo,0,0,'R');
            $pdf->Cell(18, -5, Carbon::parse($value->fven_cuo)->format('d/m/Y'),0,0,'R');
            $pdf->Cell(18, -5, $value->esta_cuo,0,0,'R');
            $pdf->Ln(1);

        }

        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(20,4,number_format($total_cuota, 2, ',', ' '),0,'C');
        $pdf->MultiCell(45,-4,number_format($total_cuota, 2, ',', ' '),0,'C');





          $pdf->Ln(10);
          //$pdf->MultiCell(60,4,'SON: '.$letras,0,'');

          $pdf->MultiCell(60,4,'ATENDIDO POR: '.$ultimo->usuario,0,'');
          $pdf->Ln(15);
          $pdf->Cell( 60,0,'','T');
          $pdf->Ln(1);
          $pdf->MultiCell(100,4,'FIRMA DEL CLIENTE: ',0,'');
          ob_get_clean();
          $pdf->Output('cuotas.pdf','I');


          //print_r($cliente);exit();


    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
