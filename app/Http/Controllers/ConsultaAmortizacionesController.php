<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Recibos;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Empresa;
use App\Cuotas;
use App\Clientes;
use App\Vendedor;

class ConsultaAmortizacionesController extends Controller
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

        return view('consulta-amortizaciones.index');
    }

    public function listamortizaciones($id,$fechauno,$fechados){

        $idsede = session('key')->sede_id;

        $recibo=Recibos::with('Amortizaciones')
        ->where('cliente_id','=',$id)
        ->where('sede_id','=',$idsede)
        ->whereBetween('fech_rec',[$fechauno,$fechados])
        ->get();

        return response()->json($recibo);



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

        $creditos=DB::table('creditos as cre')
        ->join('cuotas as cuo','cuo.credito_id','=','cre.id')
        ->join('amortizaciones as amo','amo.cuota_id','=','cuo.id')
        ->select('cre.id','cuo.mont_cuo','cuo.sald_cap','cuo.capi_cuo','amo.mont_amo','amo.tipo_amo','cuo.numero_cuo')
        ->where('amo.recibo_id','=',$id)
        ->get();

        //print_r($creditos);


        return view('consulta-amortizaciones.show',compact('creditos'));




    }

    //TRAER EL ULTIMO PAGO PARA IMPRIMIR RECIBO

    public function recibo($codigo){

        $empresa = Empresa::first();
        $ultimo=Recibos::find($codigo);
       // $ultimo=$recibo->last();
        $cliente=Clientes::where('id','=',$ultimo->cliente_id)->first();
        //vendedor
        $vendedor=Vendedor::where('id','=',$ultimo->vendedor_id)->first();

        //

        //$amortizacion=Amortizaciones::where('recibo_id','=',$ultimo->id)->get();
        $amortizacion=DB::table('amortizaciones as a')
        ->join('cuotas as c','a.cuota_id','=','c.id')
        ->where('recibo_id','=',$ultimo->id)
        ->get();

        $saldo=Cuotas::where('credito_id','=',$amortizacion[0]->credito_id)
        ->select(DB::raw('SUM(sald_cap) as saldo'),'credito_id')
        ->groupBy('credito_id')
        ->get();
        //detalle de venta
        $credito=DB::table('creditos as c')
        ->join('clientes as cl','c.cliente_id','=','cl.id')
        //->join('cuotas as c','c.credito_id','=','cre.id')
        ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
          'c.periodo_pago','cl.dire_per','c.obse_cre','id_venta')
        //->where('c.esta_cre','=','1')
        ->where('c.id','=',$saldo[0]->credito_id)
        ->first();

        

        $detalle = DB::table('detalle_venta')
        ->join('productos','detalle_venta.producto_id','=','productos.id')
        ->where('detalle_venta.venta_id','=',$credito->id_venta)
        ->get();
        //print_r(count($detalle));exit();

        $date_proximo=$this->cuota_activa($amortizacion[0]->credito_id);

       


        //print_r($amortizacion);exit();

        $pdf = new Fpdf('P','mm',array(80,220));
        $pdf->AddPage();

        $pdf->SetFont('Helvetica','',8);
        $pdf->Image('./img/logo2.jpeg',20,5,40,18);
        $pdf->Ln(15);

        //$pdf->MultiCell(60,4,$empresa['razon_social'],0,'C');
        $pdf->SetFont('Helvetica','B',11);
        $pdf->MultiCell(60,4,$empresa['nombre_comercial'],0,'C');

        $pdf->SetFont('Helvetica','',8);
        $pdf->MultiCell(60,4,utf8_decode($empresa['direccion_fiscal']),0,'C');


        $pdf->MultiCell(60,4,"RUC: ".$empresa['ruc'],0,'C');
        $pdf->Ln(1);
        //print_r($credito->obse_cre);exit();

        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(60,4,utf8_decode('RECIBO DE COBRANZA'),0,'C');
        $pdf->MultiCell(60,4,utf8_decode('N° : '.$ultimo->num_recibo),0,'C');
        
        if(isset($detalle[0]->descripcion)){
            $pdf->MultiCell(60,4,utf8_decode('PRODUCTO: '.$detalle[0]->descripcion),0,'C');
        }else{
            $pdf->MultiCell(60,4,utf8_decode('PRODUCTO: '.$credito->obse_cre),0,'C');
        }

        

        $pdf->SetFont('Helvetica','',8);

       

        $pdf->Ln(5);
        $pdf->MultiCell(60,4,'CLIENTE: '.utf8_decode($cliente->razon_social),0,'');
        $pdf->MultiCell(60,4,'DNI/RUC: '.$cliente->documento,0,'');
        $pdf->MultiCell(60,4,utf8_decode('DIRECCIÓN: '.$cliente->dire_per),0,'');
        $pdf->MultiCell(60,4,'FECHA: '.date('d-m-Y',strtotime($ultimo->fech_rec)),0,'');
        $pdf->MultiCell(60,4,'REFERENCIA: '.$cliente->referencia,0,'');
        

        $pdf->Ln(1);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->MultiCell(60,4,utf8_decode('DETALLE DE LA AMORTIZACION'),0,'C');
        $pdf->SetFont('Helvetica','',8);

        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->Cell(28, 10, utf8_decode('N° Cuota'), 0);
        $pdf->Cell(5, 10, 'Monto Cuota',0,0,'R');
        $pdf->Cell(17, 10, 'Amortizado',0,0,'R');
        $pdf->Cell(10, 10, 'Saldo',0,0,'R');
        $pdf->Ln(8);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);
        

        // PRODUCTOS
          $pdf->SetFont('Helvetica', '', 8);

           // PRODUCTOS
           $pdf->SetFont('Helvetica', '', 8);

           $total_venta = 0;

          foreach ($amortizacion as $key => $value) {

              //$total_venta += $value->cantidad * $value->precio;

              $pdf->MultiCell(5,4,'',0,'L');
              $pdf->Cell(15, -5,$value->numero_cuo);
              $pdf->Cell(20, -5, number_format($value->mont_cuo, 2, ',', ' '));
              $pdf->Cell(15, -5, number_format($value->mont_amo, 2, ',', ' '));
              $pdf->Cell(15, -5, number_format($value->saldo_cuo, 2, ',', ' '));
              $pdf->Ln(2);
          }



          $pdf->Cell(60,0,'','T');
          $pdf->Ln(2);

          $pdf->Ln(1);
          $pdf->SetFont('Helvetica', 'B', 8);
          $pdf->MultiCell(60,4,utf8_decode('TOTAL DE COBRANZA:  '). number_format($ultimo->mont_rec, 2, ',', ' '),0,'C');
          $pdf->SetFont('Helvetica','',8);


          $pdf->Ln(5);
          $pdf->SetFont('Helvetica', 'B', 8);
          $pdf->MultiCell(60,4,utf8_decode('SALDO PENDIENTE:  '). number_format($saldo[0]->saldo, 2, ',', ' '),0,'C');
          $pdf->SetFont('Helvetica','',8);

        //PONER LA FECHA PROXIMA PENDIENTE
        if(isset($date_proximo->fven_cuo)){
            $pdf->Ln(5);
            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->MultiCell(60,4,utf8_decode('PROXIMA FECHA DE PAGO:  ').$date_proximo->fven_cuo,0,'C');
            $pdf->SetFont('Helvetica','',8);
            }





          $pdf->Ln(10);
          //$pdf->MultiCell(60,4,'SON: '.$letras,0,'');

        if(isset($vendedor->nombre)){
            $pdf->MultiCell(60,4,'COBRADOR: '.$vendedor->nombre,0,'');
        }
         

          $pdf->Ln(15);
          $pdf->Cell( 60,0,'','T');
          $pdf->Ln(1);
          $pdf->MultiCell(150,4,'FIRMA DEL CAJERO(A): ',0,'');


        ob_get_clean();
        $pdf->Output('recibo.pdf','I');


         //PROMEDIO = DIVIDE(SUM(CUBO[SOLES]),DISTINCTCOUNT(CUBO[nropedido]))





    }


     //TRAER LA CUOTA PEDIENTE
     public function cuota_activa($codigo){

        $cuotas=DB::table('cuotas as c')
        ->where('c.credito_id','=',$codigo)
        ->where('c.esta_cuo','=','PENDIENTE')
        ->orderBy('c.fven_cuo','asc')
        ->first();

        return $cuotas;


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
