<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Creditos;
use App\Cuotas;
use App\Amortizaciones;
use App\Recibos;
use App\Clientes;
use DB;
use Codedge\Fpdf\Fpdf\Fpdf;
use PDF;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Empresa;
class ReporteCreditos extends Controller
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
        return view('creditos-pendientes.index');

    }

    public function listadoclientes(){


        $clientes=Clientes::where('estado_per','=','1')->get();
        return response()->json($clientes);

    }

    //METODO PARA CARGAR LOS CREDITOS PENDIENTES POR EL CLIENTE
    public function creditos($id,$estado){

      $respuesta='';

        if($estado==3){

          $respuesta=DB::table('creditos as c')
          ->join('clientes as cl','c.cliente_id','=','cl.id')
          ->join('cuotas as cu','cu.credito_id','=','c.id')
          ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
            'c.periodo_pago','cl.dire_per',DB::raw('SUM(cu.saldo_cuo) as saldo'))
          ->groupBy('c.id','cl.id','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
          'c.periodo_pago','cl.dire_per')
          //->where('c.esta_cre','=',$estado)
          ->where('c.cliente_id','=',$id)
          ->get();

        }else{

          $respuesta=DB::table('creditos as c')
        ->join('clientes as cl','c.cliente_id','=','cl.id')
        ->join('cuotas as cu','cu.credito_id','=','c.id')
        ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
          'c.periodo_pago','cl.dire_per',DB::raw('SUM(cu.saldo_cuo) as saldo'))
        ->groupBy('c.id','cl.id','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
        'c.periodo_pago','cl.dire_per')
        ->where('c.esta_cre','=',$estado)
        ->where('c.cliente_id','=',$id)
        ->get();


        }
        

        return response()->json($respuesta);

   }

   //METODO PARA DEVOLVER LAS CUOTAS
   public function cuotas($id)
   {
        $credito=DB::table('creditos as c')
        ->join('clientes as cl','c.cliente_id','=','cl.id')
        ->join('cuotas as cu','cu.credito_id','=','c.id')
        ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
        'c.periodo_pago','cl.dire_per','cu.id','cu.credito_id','cu.numero_cuo','cu.mont_cuo','cu.capi_cuo','cu.fven_cuo','cu.esta_cuo','cu.saldo_cuo')
        //->where('c.esta_cre','=','1')
        ->where('c.id','=',$id)
        //->whereIn('cu.esta_cuo', ['PENDIENTE'])
        ->orderBy('cu.numero_cuo','asc')
        ->get();

            return response()->json($credito);


   }

   //METODO PARA IMPRIMIR EN PDF EL ESTADO DE LA DEUDA
   public function estado($id,$estado){

        $credito='';

        if($estado==3){

          $credito=DB::table('creditos as c')
          ->join('clientes as cl','c.cliente_id','=','cl.id')
          ->join('cuotas as cu','cu.credito_id','=','c.id')
          ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
          'c.periodo_pago','cl.dire_per','cu.id','cu.credito_id','cu.numero_cuo','cu.mont_cuo','cu.capi_cuo','cu.fven_cuo','cu.esta_cuo','cu.saldo_cuo')
          //->where('c.esta_cre','=',$estado)
          ->where('c.cliente_id','=',$id)
  
          ->get();

        }else{

          $credito=DB::table('creditos as c')
          ->join('clientes as cl','c.cliente_id','=','cl.id')
          ->join('cuotas as cu','cu.credito_id','=','c.id')
          ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
          'c.periodo_pago','cl.dire_per','cu.id','cu.credito_id','cu.numero_cuo','cu.mont_cuo','cu.capi_cuo','cu.fven_cuo','cu.esta_cuo','cu.saldo_cuo')
          ->where('c.esta_cre','=',$estado)
          ->where('c.cliente_id','=',$id)
  
          ->get();


        }

      


        $pdf = PDF::loadView('creditos-pendientes.estado', compact('credito'));
        return $pdf->stream('estado-cuenta.pdf');


   }


   public function cuota($id){

    $empresa = Empresa::first();

      $ultimo=Creditos::find($id);

      //print_r( $ultimo->periodo_pago);exit();
     // $ultimo=$creditos->last();
      $cliente=Clientes::where('id','=',$ultimo->cliente_id)->first();
      //print_r( $cliente->conyugue);exit();

      $venta = DB::table('ventas')
      ->join('clientes','ventas.cliente_id','=','clientes.id')
      ->join('tipo_comprobantes','ventas.tipo_comprobante_id','=','tipo_comprobantes.id')
      ->where('ventas.id','=',$ultimo->id_venta)
      ->first();


      $detalle = DB::table('detalle_venta')
      ->join('productos','detalle_venta.producto_id','=','productos.id')
      ->where('detalle_venta.venta_id','=',$ultimo->id_venta)
      ->get();

      $cuota=Cuotas::where('credito_id','=',$ultimo->id)
      ->orderBy('id','asc')
      ->get();

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
      $pdf->MultiCell(60,4,utf8_decode('Concepto: Por la Compra de Mercaderia'),0,'C');
      $pdf->SetFont('Helvetica','',8);

      $pdf->Ln(5);
      $pdf->MultiCell(60,4,'CLIENTE: '.utf8_decode($cliente->razon_social),0,'');
      $pdf->MultiCell(60,4,'CONYUGUE: '.utf8_decode($cliente->conyugue),0,'');
      $pdf->MultiCell(60,4,'DNI/RUC: '.$cliente->documento,0,'');
      $pdf->MultiCell(60,4,'TELEFONO: '.$cliente->telefono,0,'');
      $pdf->MultiCell(60,4,utf8_decode('DIRECCIÓN: '.$cliente->dire_per),0,'');
      $pdf->MultiCell(60,4,utf8_decode('REFERENCIA: '.$cliente->referencia),0,'');
      $pdf->MultiCell(60,4,'PERIODO DE PAGO: '.utf8_decode($ultimo->periodo_pago),0,'');
      
      if(isset($venta->fecha)){
        $pdf->MultiCell(60,4,'FECHA: '.date('d-m-Y',strtotime($venta->fecha)),0,'');
      }

      //print_r(count($detalle));exit();

      if(count($detalle)!=0){
      
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
        $pdf->Cell(25, -5, $value->esta_cuo,0,0,'R');
        $pdf->Ln(1);

    }

    $pdf->Cell(60,0,'','T');
    $pdf->Ln(2);
    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->MultiCell(20,4,number_format($total_cuota, 2, ',', ' '),0,'C');
    $pdf->MultiCell(45,-4,number_format($total_saldo, 2, ',', ' '),0,'C');





      $pdf->Ln(10);
      //$pdf->MultiCell(60,4,'SON: '.$letras,0,'');

      $pdf->MultiCell(60,4,utf8_decode('VENDEDOR: '.$ultimo->usuario),0,'');
      $pdf->Ln(15);
      $pdf->Cell( 60,0,'','T');
      $pdf->Ln(1);
      $pdf->MultiCell(100,4,'FIRMA DEL CLIENTE: ',0,'');
      ob_get_clean();
      $pdf->Output('cuotas.pdf','I');


      //print_r($cliente);exit();


}

public function estado_cuenta($codigo,$estado){


    $empresa = Empresa::first();
    $fecha=Date('Y-m-d');

    $respuesta='';
    $var=20;



        if($estado==3){

          $respuesta=DB::table('creditos as c')
          ->join('clientes as cl','c.cliente_id','=','cl.id')
          ->join('sectores as se','se.id','=','cl.id_sector')
          ->join('cuotas as cu','cu.credito_id','=','c.id')
          ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
            'c.periodo_pago','cl.dire_per',DB::raw('SUM(cu.saldo_cuo) as saldo'),'cl.id_sector','se.nomb_sec','c.id_venta')
          ->groupBy('c.id','cl.id','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
          'c.periodo_pago','cl.dire_per','id_sector','se.nomb_sec','id_venta')
          //->where('c.esta_cre','=',$estado)
          //->where('cl.id_sector','=',$codigo_sector)
          ->where('c.cliente_id','=',$codigo)
          ->get();

        }else{

          $respuesta=DB::table('creditos as c')
        ->join('clientes as cl','c.cliente_id','=','cl.id')
        ->join('sectores as se','se.id','=','cl.id_sector')
        ->join('cuotas as cu','cu.credito_id','=','c.id')
        ->select('c.id','cl.id as codigo','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
          'c.periodo_pago','cl.dire_per',DB::raw('SUM(cu.saldo_cuo) as saldo'),'cl.id_sector','se.nomb_sec','c.id_venta')
        ->groupBy('c.id','cl.id','cl.razon_social','cl.documento','c.fpag_cre','c.esta_cre','c.impo_cre','c.peri_cre',
        'c.periodo_pago','cl.dire_per','id_sector','se.nomb_sec','id_venta')
        ->where('c.esta_cre','=',$estado)
        //->where('cl.id_sector','=',$codigo_sector)
        ->where('c.cliente_id','=',$codigo)
        ->get();
        }






         $pdf = new Fpdf('P','mm',array(80,290));
         for($i=0;$i<count($respuesta);$i++){

            $pdf->AddPage();
            $pdf->SetFont('Helvetica','',8);
            $pdf->Image('./img/logo2.jpeg',20,5,40,20);
            $pdf->Ln(15);

            $pdf->SetFont('Helvetica','B',11);
            $pdf->MultiCell(60,4,$empresa['nombre_comercial'],0,'C');

            $pdf->SetFont('Helvetica','',8);
            $pdf->MultiCell(60,4,utf8_decode($empresa['direccion_fiscal']),0,'C');

            $pdf->MultiCell(60,4,"RUC: ".$empresa['ruc'],0,'C');
            $pdf->Ln(1);

            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->MultiCell(60,4,utf8_decode('TARGETA DE CREDITO'),0,'C');
            $pdf->MultiCell(60,4,utf8_decode('N° : '.$respuesta[$i]->id),0,'C');
            $pdf->SetFont('Helvetica','',8);



            $pdf->Ln(5);
           $pdf->MultiCell(60,4,'DNI/RUC: '.$respuesta[$i]->documento,0,'');
           $pdf->MultiCell(60,4,'CLIENTE: '.utf8_decode($respuesta[$i]->razon_social),0,'');
           $pdf->MultiCell(60,4,utf8_decode('DIRECCIÓN: '.$respuesta[$i]->dire_per),0,'');
           $pdf->MultiCell(60,4,utf8_decode('SECTOR: '.$respuesta[$i]->nomb_sec),0,'');
           $pdf->MultiCell(60,4,utf8_decode('REFERENCIA: '),0,'');
           $pdf->MultiCell(60,4,'FECHA: '.$fecha,0,'');
           $pdf->MultiCell(60,4,'MONTO DEL CREDITO: '.$respuesta[$i]->impo_cre,0,'');
           $pdf->MultiCell(60,4,'SALDO PENDIENTE: '.$this->saldos_total($respuesta[$i]->id),0,'');
           $pdf->MultiCell(60,4,'MONTO VENCIDO: '.$respuesta[$i]->saldo,0,'');
           $pdf->MultiCell(60,4,'CONDICION DE PAGO: '.$respuesta[$i]->periodo_pago,0,'');
           $pdf->MultiCell(60,4,'PERIODO: '.$respuesta[$i]->peri_cre,0,'');
           $pdf->MultiCell(60,4,'COBRADOR: ',0,'');


           $pdf->Ln(1);
           $pdf->SetFont('Helvetica', 'B', 8);
           $pdf->MultiCell(60,4,utf8_decode('PRODUCTO (S) OTORGADO (S)'),0,'C');
           $pdf->SetFont('Helvetica','',8);

           $pdf->SetFont('Helvetica', 'B', 8);
           $pdf->Cell(28, 10, utf8_decode('Descripción'), 0);
           $pdf->Cell(5, 10, 'Und',0,0,'R');
           $pdf->Cell(10, 10, 'Precio',0,0,'R');
           $pdf->Cell(15, 10, 'Total',0,0,'R');
           $pdf->Ln(8);
           $pdf->Cell(60,0,'','T');
           $pdf->Ln(2);

           $detalle_producto=$this->detalle_venta($respuesta[$i]->id_venta);

              // PRODUCTOS
          $pdf->SetFont('Helvetica', '', 8);

           $total_venta = 0;

        foreach ($detalle_producto as $key => $value) {

        $total_venta += $value->cantidad * $value->precio;

            $pdf->MultiCell(30,4,utf8_decode($value->nomb_pro),0,'L');
            $pdf->Cell(32, -5, $value->cantidad,0,0,'R');
            $pdf->Cell(15, -5, number_format($value->precio, 2, '.', ' '));
            $pdf->Cell(15, -5, "S/ ".number_format($value->precio * $value->cantidad, 2, '.', ' '));
            $pdf->Ln(2);
     }








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

      $cuotax=$this->cuotas_data($respuesta[$i]->id);

      foreach ($cuotax as $key => $value) {

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
            $pdf->MultiCell(45,-4,number_format($total_saldo, 2, ',', ' '),0,'C');

            $pdf->Ln(10);

            $pdf->MultiCell(60,4,'VENDEDOR: ',0,'');
            $pdf->Ln(15);
             $pdf->Cell( 60,0,'','T');
             $pdf->Ln(1);
            $pdf->MultiCell(100,4,'FIRMA DEL CLIENTE: ',0,'');



         }




         ob_get_clean();
         $pdf->Output('masivo.pdf','I');



}

 //EL TOTAL DE LOS SALDOS DE CADA CREDITO

 public function saldos_total($codigo){

    $saldo=DB::table('cuotas as cu')
    ->select(DB::raw('SUM(cu.saldo_cuo) as saldo'))
    ->where('cu.credito_id','=',$codigo)
    ->get();

    return $saldo[0]->saldo;
}



//EL DETALLE DE LA VENTA DEL CREDITO GENERADO
public function detalle_venta($codigo){

  $detalle = DB::table('detalle_venta')
  ->join('productos','detalle_venta.producto_id','=','productos.id')
  ->where('detalle_venta.venta_id','=',$codigo)
  ->get();

  return $detalle;



}

//METODO PARA SACAR TODAS LAS CUOTAS
public function cuotas_data($codigo){

  $cuota=Cuotas::where('credito_id','=',$codigo)
  ->orderBy('id','asc')
  ->get();

  return $cuota;

}










}
