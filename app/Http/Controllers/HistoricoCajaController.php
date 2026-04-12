<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Caja;
use App\User;
use App\Movimientos;
use App\Conceptos;
use App\Detalle_venta;
use App\Venta;
use App\Productos;
use App\Categorias;
use Illuminate\Support\Facades\DB;
use App\Empresa;
use Codedge\Fpdf\Fpdf\Fpdf;

class HistoricoCajaController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;
        
        $cajas = DB::table('caja')
        ->join('users','caja.user_id','=','users.id')
        ->select('caja.id','caja.sede_id','users.name', 'caja.fecha_apertura','caja.hora_apertura','caja.fecha_cierre','caja.hora_cierre','caja.monto_apertura','caja.monto_cierre_fisico','caja.monto_cierre_virtual', 'caja.estado')
        ->where('caja.sede_id','=',$idsede)
        ->orderBy('caja.fecha_apertura', 'desc')
        ->get();
        //print_r($cajas); exit();

        return view('historico-caja.index',compact('cajas'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pdfcaja($idcajafisica)
    {
        $empresa = Empresa::first();
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;
        
        $cajas = DB::table('caja')
        ->join('users','caja.user_id','=','users.id')
        ->select('caja.id','caja.sede_id','users.name', 'caja.fecha_apertura','caja.hora_apertura','caja.fecha_cierre','caja.hora_cierre','caja.monto_apertura','caja.monto_cierre_fisico','caja.monto_cierre_virtual')
        ->where('caja.sede_id','=',$idsede)
        ->where('caja.id', '=', $idcajafisica)->first();
        /* echo '<pre>';
        var_dump($cajas); die; */
        

        $movimientos = DB::table('movimientos')
        ->join('caja','movimientos.id_sesion_caja','=','caja.id')
        ->join('conceptos','movimientos.concepto_id','=','conceptos.id')
        ->select('movimientos.id_sesion_caja', DB::raw('SUM(movimientos.monto) as monto'), 'conceptos.tipo_movimiento')
        ->where('movimientos.sede_id', '=', $idsede)
        ->where('movimientos.id_sesion_caja','=',$idcajafisica)
        ->where('conceptos.tipo_movimiento', '=', 'INGRESO')
        -> groupBy('movimientos.id_sesion_caja','conceptos.tipo_movimiento')
        ->get();

        //print_r($movimientos); exit;
        
        $categorias = DB::table('detalle_venta')
        ->join('ventas','detalle_venta.venta_id','=','ventas.id')
        ->join('venta_formapago','ventas.id','=','venta_formapago.venta_id')
        ->join('movimientos','venta_formapago.movimiento_id','=','movimientos.id')
        ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
        ->join('sedes','movimientos.sede_id','=','sedes.id')
        ->join('productos','detalle_venta.producto_id','=','productos.id')
        ->join('categorias','productos.categoria_id','=','categorias.id')
        ->select(DB::raw('DISTINCT(productos.categoria_id)'), 'categorias.categoria', 'categorias.id', DB::raw('SUM(detalle_venta.cantidad) as cantidad'), DB::raw('SUM(detalle_venta.subtotal) as subtotal'))
        //->select('detalle_venta.venta_id', 'productos.nomb_pro', 'detalle_venta.cantidad', 'movimientos.id', 'movimientos.sede_id', 'movimientos.id_sesion_caja', 'categorias.categoria')
        ->where('movimientos.sede_id', '=', $idsede)
        ->where('movimientos.id_sesion_caja', '=', $idcajafisica)
        ->where('conceptos.tipo_movimiento', '=', 'INGRESO')
        -> groupBy('categorias.categoria','categorias.id','productos.categoria_id')
        ->get();

        /* echo '<pre>';
        print_r($categorias);exit;*/

        $detalle_venta = DB::table('detalle_venta')
        ->join('ventas','detalle_venta.venta_id','=','ventas.id')
        ->join('venta_formapago','ventas.id','=','venta_formapago.venta_id')
        ->join('movimientos','venta_formapago.movimiento_id','=','movimientos.id')
        ->join('sedes','movimientos.sede_id','=','sedes.id')
        ->join('productos','detalle_venta.producto_id','=','productos.id')
        ->join('unidad_medidas', 'productos.unidad_medida_id', '=', 'unidad_medidas.id')
        ->join('categorias','productos.categoria_id','=','categorias.id')
        ->select('detalle_venta.venta_id', 'productos.nomb_pro', 'detalle_venta.cantidad', 'categorias.categoria','productos.categoria_id', 'detalle_venta.precio', 'movimientos.id_sesion_caja', 'movimientos.id', 'detalle_venta.subtotal', 'unidad_medidas.abrev', 'ventas.venta_estado')
        ->where('movimientos.sede_id', '=', $idsede)
        ->where('movimientos.id_sesion_caja', '=', $idcajafisica)
        ->get();

       //var_dump($detalle_venta); die;
        
        $total_pagos = DB:: table('movimientos')
        ->join('caja', 'movimientos.id_sesion_caja', '=', 'caja.id')
        ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
        ->join('recibos', 'movimientos.id', '=', 'recibos.id_movimiento')
        ->join('clientes', 'recibos.cliente_id', '=', 'clientes.id')
        ->select(DB::raw('SUM(recibos.mont_rec) as total'))
        ->where('movimientos.sede_id', '=', $idsede)
        ->where('movimientos.id_sesion_caja', '=', $idcajafisica)
        ->where('movimientos.descripcion', '=', 'PAGO DE CUOTAS DE CREDITO')
        ->get();

        //var_dump($total_pagos); die;

        $ingresos_pagos = DB:: table('movimientos')
        ->join('caja', 'movimientos.id_sesion_caja', '=', 'caja.id')
        ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
        ->join('recibos', 'movimientos.id', '=', 'recibos.id_movimiento')
        ->join('clientes', 'recibos.cliente_id', '=', 'clientes.id')
        ->select('movimientos.descripcion', 'movimientos.descripcion_comprobante', 'recibos.mont_rec as monto', 'clientes.razon_social')
        ->where('movimientos.sede_id', '=', $idsede)
        ->where('movimientos.id_sesion_caja', '=', $idcajafisica)
        ->where('movimientos.descripcion', '=', 'PAGO DE CUOTAS DE CREDITO')
        ->get();
        /* echo '<pre>';
        print_r($ingresos_pagos);exit; */

        $egresos = DB:: table ('movimientos')
        ->join('caja', 'movimientos.id_sesion_caja', '=', 'caja.id')
        ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
        ->select('movimientos.monto', 'conceptos.descripcion')
        ->where('movimientos.sede_id', '=', $idsede)
        ->where('movimientos.id_sesion_caja', '=', $idcajafisica)
        ->where('conceptos.tipo_movimiento', '=', 'EGRESO')
        ->get();
        

          //$creditos=Creditos::orderBy('id','asc')->get();
          //$ultimo=$creditos->last();
          //$cliente=Clientes::where('id','=',$ultimo->cliente_id)->first();

          $pdf = new Fpdf('P','mm',array(210,297));
          $pdf->AddPage();
          $pdf->SetLeftMargin(20);
          $pdf->SetRightMargin(20);

          $pdf->SetFont('Helvetica','',8);
          $pdf->Image('./img/logo2.jpeg',90,20,30,20);
          $pdf->Ln(38);
          
          //$pdf->setX(55);
          
          $pdf->SetFont('Helvetica','B',15);
          $pdf->SetDrawColor(121, 125, 127);
          $pdf->SetTextColor(121, 125, 127);
          $pdf->Cell(0,8,"HISTORIAL DE ARQUEO DE CAJA",'T,B',0,'C');
          $pdf->Ln(6);
          //$pdf->MultiCell(60,4,$empresa['razon_social'],0,'C');
          $pdf->Ln(15);
          $pdf->SetX(130);
          $pdf->SetFont('Helvetica','B',11);
          $pdf->SetTextColor(6, 6, 6);
          $pdf->MultiCell(60,4,$empresa['nombre_comercial'],0,'R');

          $pdf->SetX(130);
          $pdf->SetFont('Helvetica','',8);
          $pdf->MultiCell(60,4,$empresa['direccion_fiscal'],0,'R');

          $pdf->SetX(130);
          $pdf->MultiCell(60,4,"RUC: ".$empresa['ruc'],0,'R');
          
          $pdf->Ln(1);
          $pdf->SetLeftMargin(20);
          $pdf->SetRightMargin(20);
          $pdf->SetFont('Arial', '', 10);
          $pdf->MultiCell(60,4,'Cajero00: '.utf8_decode($cajas->name),0,'L');

          $pdf->SetLeftMargin(20);
          $pdf->SetRightMargin(20);
          $pdf->MultiCell(60,4,'Monto Apertura Fisico: '.utf8_decode($cajas->monto_apertura),0,'L');
          
          $pdf->SetLeftMargin(20);
          $pdf->SetRightMargin(20);
          $pdf->MultiCell(60,4,'Monto Cierre Fisico: '.utf8_decode($cajas->monto_cierre_fisico),0,'L');
          
          $pdf->SetLeftMargin(20);
          $pdf->SetRightMargin(20);
          $pdf->MultiCell(60,4,'Monto Apertura Virtual: ',0,'L');
          
          $pdf->SetLeftMargin(20);
          $pdf->SetRightMargin(20);
          $pdf->MultiCell(60,4,'Monto Cierre Virtual: '.utf8_decode($cajas->monto_cierre_virtual),0,'L');
          $pdf->SetFont('Arial','',8);

          $pdf->Ln(15);
          $pdf->SetX(15);
          
          $pdf->SetLeftMargin(20);
          $pdf->SetRightMargin(20);
          
          $pdf->SetFont('Helvetica','B',10);
          $pdf->Cell(170, 5, utf8_decode('VENTAS'),'B',1,'L');
          $pdf->Ln(0.6);
          $pdf->SetFont('Arial','B',8);
          $pdf->SetTextColor(77, 86, 86 );
          $pdf->Cell(85, 5, utf8_decode('DESCRIPCIÓN'),'B',0,'L',0);
          $pdf->Cell(20, 5, utf8_decode('CANT.'),'B',0,'C',0);
          $pdf->Cell(20, 5, utf8_decode('U.M'),'B',0,'L',0);
          $pdf->Cell(22.5, 5, utf8_decode('P.UNIT'),'B',0,'L',0);
          $pdf->Cell(22.5, 5, utf8_decode('TOTAL'),'B',1,'L',0);
        
          $pdf->Ln(0.6);
          

          for($i=0; $i< count($categorias); $i++ ){

                $pdf->SetTextColor(27, 38, 49 );
                $pdf->SetFillColor(229, 232, 232 );
                $pdf->SetFont('Helvetica','',9);
                $pdf->Cell(85, 8, utf8_decode($categorias[$i]->categoria),'',0,'L',1);
                $pdf->Cell(20, 8, $categorias[$i]->cantidad,'',0,'C',1);
                $pdf->Cell(20, 8, utf8_decode(''),'',0,'L',1);
                $pdf->Cell(22.5, 8, utf8_decode(''),'',0,'L',1);
                $pdf->Cell(22.5, 8, $categorias[$i]->subtotal,'',1,'L',1);

                for($k=0; $k < count($detalle_venta); $k++){
                    if (($categorias[$i]->id)==($detalle_venta[$k]->categoria_id)) {
                        $pdf->SetTextColor(27, 38, 49 );
                        $pdf->SetFillColor(248, 249, 249 );
                        $pdf->SetFont('Helvetica','',8);
                        $pdf->Cell(85, 8, utf8_decode( $detalle_venta[$k]->nomb_pro),'',0,'L',1);
                        $pdf->Cell(20, 8, $detalle_venta[$k]->cantidad,'',0,'C',1);
                        $pdf->Cell(20, 8, utf8_decode( $detalle_venta[$k]->abrev),'',0,'L',1);
                        $pdf->Cell(22.5, 8, $detalle_venta[$k]->precio,'',0,'L',1);
                        $pdf->Cell(22.5, 8,$detalle_venta[$k]->subtotal,'',1,'L',1);
                    
                        
                    }
               } 
            
            
          }

          if(count($ingresos_pagos)>0){
            $pdf->Ln(15);
            $pdf->SetX(15);
            
            $pdf->SetLeftMargin(20);
            $pdf->SetRightMargin(20);

            $pdf->SetFont('Helvetica','B',10);
            $pdf->Ln(0.6);
            $pdf->Cell(170, 5, utf8_decode('PAGO DE CUOTAS DE CRÉDITOS'),'B',1,'L');
            $pdf->Ln(0.6);
            $pdf->SetFont('Arial','B',8);
            $pdf->SetTextColor(77, 86, 86 );
            $pdf->Cell(50, 5, utf8_decode('DESCRIPCIÓN'),'B',0,'L',0);
            $pdf->Cell(70, 5, utf8_decode('CLIENTE'),'B',0,'L',0);
            $pdf->Cell(20, 5, utf8_decode('Nro.RECIBO'),'B',0,'L',0);
            $pdf->Cell(30, 5, utf8_decode('MONTO'),'B',1,'C',0);

            $pdf->Ln(0.6);
            
            $pdf->SetTextColor(27, 38, 49 );
            $pdf->SetFillColor(248, 249, 249 );
            $pdf->SetFont('Helvetica','',8);

            for ($i=0; $i < count($ingresos_pagos); $i++) { 
                $pdf->SetTextColor(27, 38, 49 );
                $pdf->SetFillColor(242, 243, 244 );
                $pdf->SetFont('Helvetica','',8);
                $pdf->Cell(50, 8, utf8_decode($ingresos_pagos[$i]->descripcion),'',0,'L',1);
                $pdf->Cell(70, 8, utf8_decode($ingresos_pagos[$i]->razon_social),'',0,'L',1);
                $pdf->Cell(20, 8, $ingresos_pagos[$i]->descripcion_comprobante,'',0,'C',1);
                $pdf->Cell(30, 8, $ingresos_pagos[$i]->monto,'',1,'C',1);
            }
            $pdf->SetTextColor(27, 38, 49 );
            $pdf->SetFillColor(229, 232, 232 );
            $pdf->SetFont('Helvetica','',9);
            $pdf->Cell(85, 8, utf8_decode('TOTAL'),'',0,'L',1);
            $pdf->Cell(20, 8, '','',0,'C',1);
            $pdf->Cell(20, 8, utf8_decode(''),'',0,'L',1);
            $pdf->Cell(22.5, 8, utf8_decode(''),'',0,'L',1);
            $pdf->Cell(22.5, 8, $total_pagos[0]->total,'',1,'L',1);
          }

          if (count($egresos)>0) {
            $pdf->Ln(15);
            $pdf->SetX(15);
            
            $pdf->SetLeftMargin(20);
            $pdf->SetRightMargin(20);

            $pdf->SetFont('Helvetica','B',10);
            $pdf->Ln(0.6);
            $pdf->Cell(170, 5, utf8_decode('EGRESOS'),'B',1,'L');
            $pdf->Ln(0.6);
            $pdf->SetFont('Arial','B',8);
            $pdf->SetTextColor(77, 86, 86 );
            $pdf->Cell(85, 5, utf8_decode('DESCRIPCIÓN'),'B',0,'L',0);
            $pdf->Cell(85, 5, utf8_decode('TOTAL'),'B',1,'C',0);

            $pdf->Ln(0.6);
            
            $pdf->SetTextColor(27, 38, 49 );
            $pdf->SetFillColor(248, 249, 249 );
            $pdf->SetFont('Helvetica','',8);

            for ($i=0; $i < count($egresos); $i++) { 
                $pdf->SetTextColor(27, 38, 49 );
                $pdf->SetFillColor(242, 243, 244 );
                $pdf->SetFont('Helvetica','',8);
                $pdf->Cell(85, 8, utf8_decode($egresos[$i]->descripcion),'',0,'L',1);
                $pdf->Cell(85, 8, $egresos[$i]->monto,'',1,'C',1);
            }
          }
          

         
          
         
          /* foreach($categorias as $key => $catg){
            $pdf->Cell(80, 8, $catg->categoria,'',0,'L',1);
            $pdf->Cell(22.5, 8, utf8_decode('4'),'',0,'L',1);
            $pdf->Cell(22.5, 8, utf8_decode(''),'',0,'L',1);
            $pdf->Cell(22.5, 8, utf8_decode(''),'',0,'L',1);
            $pdf->Cell(22.5, 8, utf8_decode('12.00'),'',1,'L',1);
            } */
          

          
         /*  $pdf->SetFont('Helvetica','',10);
          $pdf->SetTextColor(27, 38, 49 );
          $pdf->SetFillColor(248, 249, 249);
          $pdf->Cell(80, 8, utf8_decode('Inka Kola'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('2'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('UND'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('3.00'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('6.00'),'',1,'L',1);

          $pdf->SetFont('Helvetica','',10);
          $pdf->SetTextColor(27, 38, 49 );
          $pdf->SetFillColor(242, 243, 244);
          $pdf->Cell(80, 8, utf8_decode('Coca Cola'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('2'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('UND'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('3.00'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('6.00'),'',1,'L',1);

          $pdf->SetFont('Helvetica','B',10);
          $pdf->SetTextColor(27, 38, 49 );
          $pdf->SetFillColor(248, 249, 249);
          $pdf->Cell(80, 8, utf8_decode('JUGOS CLASICOS'),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode(''),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode(''),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode(''),'',0,'L',1);
          $pdf->Cell(22.5, 8, utf8_decode('12.00'),'',1,'L',1);
          $pdf->SetFillColor(233, 229, 235);//color de fondo rgb
            $pdf->SetDrawColor(61, 61, 61);//color de linea  rgb */

            /* $pdf->SetFont('Arial','',12);
            for($i=1;$i<=50;$i++){
                
                $pdf->Ln(0.6);
                $pdf->setX(15);
            $pdf->Cell(10,8,$i,'B',0,'C',1);
            $pdf->Cell(60,8,'Leche','B',0,'C',1);
            $pdf->Cell(30,8,'$'.'20','B',0,'C',1);
            $pdf->Cell(35,8,'2','B',0,'C',1);
            $pdf->Cell(50,8,'40','B',1,'C',1);

            } */
          /*$pdf->MultiCell(60,4,'CLIENTE: '.utf8_decode($venta->razon_social),0,'');
          $pdf->MultiCell(60,4,'DNI/RUC: '.$venta->documento,0,'');
          $pdf->MultiCell(60,4,utf8_decode('DIRECCIÓN: '.$venta->dire_per),0,'');
          $pdf->MultiCell(60,4,'FECHA: '.date('d-m-Y',strtotime($venta->fecha)),0,'');*/

          $pdf->Ln(1);
          
         
          
          // PRODUCTOS
         $pdf->SetFont('Helvetica', '', 8);

        ob_get_clean();
        $pdf->Output('recibo.pdf','I');
    }

    public function resumenCaja($id_caja, $fecha_apertura, $fecha_cierre){
        $empresa = Empresa::first();
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        //var_dump($id_caja);die;

        $cajas = DB::table('caja')
        ->join('users','caja.user_id','=','users.id')
        ->select('caja.id','caja.sede_id','users.name', 'caja.fecha_apertura','caja.hora_apertura','caja.fecha_cierre','caja.hora_cierre','caja.monto_apertura','caja.monto_cierre_fisico','caja.monto_cierre_virtual')
        ->where('caja.sede_id','=',$idsede)
        ->where('caja.id', '=', $id_caja)->first();
        //echo '<pre>';
        //var_dump($cajas); die;
          
       //var_dump($detalle_venta); die;
        //Pago de Cuotas
        $pago_cuota = DB::table('movimientos as m')
        ->join('caja as c', 'm.id_sesion_caja', '=', 'c.id')
        ->join('conceptos as cn', 'm.concepto_id', '=', 'cn.id')
        ->select(DB::raw('SUM(m.monto) as pago_cuota'))
        ->where('m.id_sesion_caja', '=', $id_caja)
        ->where('c.fecha_apertura', '=', $fecha_apertura)
        ->where('c.fecha_cierre', '=', $fecha_cierre)
        ->where('cn.id', '=', 1)
        ->where('cn.tipo_movimiento', '=', 'INGRESO')
        ->where('m.sede_id', '=', $idsede)
        ->get();

        //var_dump($pago_cuota); die;

        //Consulta para ventas al contado (Notas de venta)
        $venta_contado = DB::table('movimientos as m')
            ->join('caja as c', 'm.id_sesion_caja', '=', 'c.id')
            ->join('tipo_comprobantes as tc', 'm.tipo_comprobante_id', '=', 'tc.id')
            ->join('venta_formapago as vf', 'm.id', '=', 'vf.movimiento_id')
            ->join('ventas as v', 'vf.venta_id', '=', 'v.id')
            ->select(DB::raw('SUM(m.monto) as venta_contado'))
            ->where('m.id_sesion_caja', '=', $id_caja)
            ->where('c.fecha_apertura', '=', $fecha_apertura)
            ->where('c.fecha_cierre', '=', $fecha_cierre)
            ->where('tc.id', '=', 5)
            ->where('m.sede_id', '=', $idsede)
            ->where('v.tipo_pago_id', '=', 1)
            ->get();

        //var_dump($venta_contado); die;
         
        //Consulta para obtener el total de boletas de venta electrónico
        $boleta_electronica = DB::table('movimientos as m')
            ->join('caja as c', 'm.id_sesion_caja', '=', 'c.id')
            ->join('tipo_comprobantes as tc', 'm.tipo_comprobante_id', '=', 'tc.id')
            ->join('venta_formapago as vf', 'm.id', '=', 'vf.movimiento_id')
            ->join('ventas as v', 'vf.venta_id', '=', 'v.id')
            ->join('conceptos as cn', 'm.concepto_id', '=', 'cn.id')
            ->select(DB::raw('SUM(m.monto) as boleta_electronica'))
            ->where('m.id_sesion_caja', '=', $id_caja)
            ->where('c.fecha_apertura', '=', $fecha_apertura)
            ->where('c.fecha_cierre', '=', $fecha_cierre)
            ->where('tc.id', '=', 1)
            ->where('m.sede_id', '=', $idsede)
            ->where('v.tipo_pago_id', '=', 1)
            ->where('cn.id', '=', 9)
            ->get(); 

            //var_dump($boleta_electronica); die;
        //consulta para las facturas electrónicas
        $factura_electronica = DB::table('movimientos as m')
            ->join('caja as c', 'm.id_sesion_caja', '=', 'c.id')
            ->join('tipo_comprobantes as tc', 'm.tipo_comprobante_id', '=', 'tc.id')
            ->join('venta_formapago as vf', 'm.id', '=', 'vf.movimiento_id')
            ->join('ventas as v', 'vf.venta_id', '=', 'v.id')
            ->select(DB::raw('SUM(m.monto) as factura_electronica'))
            ->where('m.id_sesion_caja', '=', $id_caja)
            ->where('c.fecha_apertura', '=', $fecha_apertura)
            ->where('c.fecha_cierre', '=', $fecha_cierre)
            ->where('tc.id', '=', 2)
            ->where('m.sede_id', '=', $idsede)
            ->where('v.tipo_pago_id', '=', 1)
            ->get();

        //CONSULTA PARA EL MONTO DE OTROS INGRESOS
        $Otros_total = DB::table('movimientos')
        ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
        ->join('caja', 'movimientos.id_sesion_caja', '=', 'caja.id')
        ->join('sedes', 'movimientos.sede_id', '=', 'sedes.id')
        ->select(DB::raw('SUM(movimientos.monto) as otros'))
        ->where('caja.id', '=', $id_caja)
        ->where('sedes.id', '=', $idsede)
        ->where('conceptos.tipo_movimiento', '=', 'INGRESO')
        ->where('movimientos.descripcion_comprobante', '=', "")
        ->where('caja.fecha_apertura', '=', $fecha_apertura)
        ->where('caja.fecha_cierre', '=', $fecha_cierre)
        ->get();

        //CONSULTA PARA LOS OTROS INGRESOS DETALLADOS
        $otros_ingresos_detallado = DB::table('movimientos')
        ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
        ->join('caja', 'movimientos.id_sesion_caja', '=', 'caja.id')
        ->join('sedes', 'movimientos.sede_id', '=', 'sedes.id')
        ->where('caja.id', '=', $id_caja)
        ->where('sedes.id', '=', $idsede)
        ->where('conceptos.tipo_movimiento', '=', 'INGRESO')
        ->where('movimientos.descripcion_comprobante', '=', "")
        ->where('caja.fecha_apertura', '=', $fecha_apertura)
        ->where('caja.fecha_cierre', '=', $fecha_cierre)
        ->get();

        //echo "<pre>"; print_r($otros_ingresos_detallado); exit;

        //var_dump($Otros_total); die;

        //METODO PARA SUMAR EL MONTO TOTAL DE LOS INGRESOS
        $coleccion1 = collect($pago_cuota);
        $coleccion2 = collect($venta_contado);
        $coleccion3 = collect($boleta_electronica);
        $coleccion4 = collect($factura_electronica);
        $coleccion5 = collect($Otros_total);

        $monto_total = $coleccion1->sum('pago_cuota') + $coleccion2->sum('venta_contado') + $coleccion3
        ->sum('boleta_electronica') + $coleccion4->sum('factura_electronica') + $coleccion5->sum('otros');

        //var_dump($monto_total); die;

        $egresos = DB:: table ('movimientos')
        ->join('caja', 'movimientos.id_sesion_caja', '=', 'caja.id')
        ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
        ->select('movimientos.monto as egreso_monto', 'movimientos.descripcion as descripcion')
        ->where('movimientos.sede_id', '=', $idsede)
        ->where('caja.id', '=', $id_caja)
        ->where('caja.fecha_apertura', '=', $fecha_apertura)
        ->where('caja.fecha_cierre', '=', $fecha_cierre)
        ->where('conceptos.tipo_movimiento', '=', 'EGRESO')
        ->get();

        //consulta de los que han cobrado cuotas y registrado en un repectivo responsable de caja
        $cobro_cuotas = DB::table('recibos as r')
            ->join('vendedores as v', 'r.vendedor_id', '=', 'v.id')
            ->join('movimientos as m', 'r.id_movimiento', '=', 'm.id')
            ->join('caja as c', 'm.id_sesion_caja', '=', 'c.id')
            ->select(DB::raw('SUM(r.mont_rec) as total_cobros, v.nombre'))
            ->where('c.id', '=', $id_caja)
            ->where('c.fecha_apertura', '=', $fecha_apertura)
            ->where('c.fecha_cierre', '=', $fecha_cierre)
            ->where('m.sede_id', '=', $idsede)
            ->groupBy('v.nombre')
            ->get();

        //var_dump($cobro_cuotas); die;


          $pdf = new Fpdf('P','mm',array(210,297));
          $pdf->SetMargins(20, 20);
          $pdf->AddPage();
          $pdf->SetLeftMargin(20);
          $pdf->SetRightMargin(20);

          $pdf->SetFont('Helvetica','',8);
          $pdf->Image('./img/logo2.jpeg',90,20,30,20);
          $pdf->Ln(22);
          
          //$pdf->setX(55);
          
          $pdf->SetFont('Helvetica','B',15);
          $pdf->SetDrawColor(121, 125, 127);
          $pdf->SetTextColor(121, 125, 127);
          $pdf->Cell(0,8,"CONTROL DE INGRESOS Y GASTOS",'T,B',0,'C');
          $pdf->Ln(3);
          //$pdf->MultiCell(60,4,$empresa['razon_social'],0,'C');
          $pdf->Ln(7);
          $pdf->SetX(130);
          $pdf->SetFont('Helvetica','B',11);
          $pdf->SetTextColor(6, 6, 6);
          $pdf->MultiCell(60,4,$empresa['nombre_comercial'],0,'R');

          $pdf->SetX(130);
          $pdf->SetFont('Helvetica','',8);
          $pdf->MultiCell(60,4, utf8_decode($empresa['direccion_fiscal']),0,'R');

          $pdf->SetX(130);
          $pdf->MultiCell(60,4,"RUC: ".$empresa['ruc'],0,'R');

          $pdf->Ln(1);

          $pdf->SetFont('Arial', 'B', 10);
          $pdf->Cell(37, 4, 'Cajero responsable:', 0, 0, 'L'); 
          $pdf->SetFont('Arial', '', 10);
          $pdf->Cell(40, 4, utf8_decode($cajas->name), 0, 0, 'L', 0);
          
          $pdf->Ln();

          $pdf->SetFont('Arial', 'B', 10); 
          $pdf->Cell(42, 4, 'Monto Apertura Fisico:', 0, 0, 'L'); 
          $pdf->SetFont('Arial', '', 10); 
          $pdf->Cell(40, 4, utf8_decode($cajas->monto_apertura), 0, 0, 'L', 0);
          
          $pdf->Ln();

          $pdf->SetFont('Arial', 'B', 10); 
          $pdf->Cell(38, 4, 'Monto Cierre Fisico:', 0, 0, 'L'); 
          $pdf->SetFont('Arial', '', 10); 
          $pdf->Cell(40, 4, utf8_decode($cajas->monto_cierre_fisico), 0, 0, 'L', 0);
          
          $pdf->Ln();

          $pdf->SetFont('Arial', 'B', 10); 
          $pdf->Cell(42, 4, 'Monto Apertura Virtual: ', 0, 0, 'L'); 
          $pdf->SetFont('Arial', '', 10); 
          $pdf->Cell(40, 4, 0, 0, 0, 'L', 0);
          
          $pdf->Ln();
          
          $pdf->SetFont('Arial', 'B', 10); 
          $pdf->Cell(38, 4, 'Monto Cierre Virtual: ', 0, 0, 'L'); 
          $pdf->SetFont('Arial', '', 10); 
          $pdf->Cell(40, 4, utf8_decode($cajas->monto_cierre_virtual), 0, 0, 'L', 0);

          $pdf->Ln();
          
          $pdf->SetFont('Arial', 'B', 10); 
          $pdf->Cell(30, 4, 'Fecha apertura: ', 0, 0, 'L'); 
          $pdf->SetFont('Arial', '', 10); 
          $pdf->Cell(40, 4, utf8_decode(date('d-m-Y', strtotime($cajas->fecha_apertura))), 0, 0, 'L', 0);

          $pdf->Ln();
          
          $pdf->SetFont('Arial', 'B', 10); 
          $pdf->Cell(26, 4, 'Fecha cierre: ', 0, 0, 'L'); 
          $pdf->SetFont('Arial', '', 10); 
          $pdf->Cell(40, 4, utf8_decode(date('d-m-Y', strtotime($cajas->fecha_cierre))), 0, 0, 'L', 0);

          $pdf->Ln(10);

          // Configuramos la fuente y el tamaño de letra
            $pdf->SetFont('Helvetica','B',12);

            // Imprimimos la primera tabla
            $pdf->Cell(170, 4, utf8_decode('Ingresos'),'B',1,'C');
           

            // Salto de línea para iniciar la segunda fila
            $pdf->Ln(0.6);

            // Imprimimos los encabezados de la primera tabla
            $pdf->SetFont('Arial','B',11);
            $pdf->SetTextColor(77, 86, 86 );
            $pdf->Cell(10, 4, '#','B',0,'L',0);
            $pdf->Cell(125, 4, ('Concepto'),'B',0,'L',0);
            $pdf->Cell(35, 4, ('Total'),'B',1,'L',0);
            
            $pdf->Ln(0.6);
            $pdf->SetTextColor(27, 38, 49 );
            $pdf->SetFillColor(248, 249, 249 ); 
            $pdf->SetFont('Helvetica','',10);

            $pdf->Cell(10, 4, '1','',0,'L',1);
            $pdf->Cell(125, 4, ('Pago de Cuotas'),'',0,'L',1);
        
            if ($pago_cuota[0]->pago_cuota == null) {
                $pdf->Cell(35, 4, ('0.00'),'',1,'L',1);
            }else{
                $pdf->Cell(35, 4, $pago_cuota[0]->pago_cuota,'',1,'L',1);
               
            }
            
            $pdf->Cell(10, 4, '2','',0,'L',1);
            $pdf->Cell(125, 4, utf8_decode('Ventas al contado (Notas de venta)'),'',0,'L',1);

            if ($venta_contado[0]->venta_contado == null) {
                $pdf->Cell(35, 4, ('0.00'),'',1,'L',1);
            }else{
                $pdf->Cell(35, 4, $venta_contado[0]->venta_contado,'',1,'L',1);
                
            }
            
            $pdf->Cell(10, 4, '3','',0,'L',1);
            $pdf->Cell(125, 4,  utf8_decode('Boletas de ventas electrónico'),'',0,'L',1);

            if ($boleta_electronica[0]->boleta_electronica == null) {
                $pdf->Cell(35, 4, ('0.00'),'',1,'L',1);
            }else{
                $pdf->Cell(35, 4, $boleta_electronica[0]->boleta_electronica,'',1,'L',1);
                
            }
            
            $pdf->Cell(10, 4, '4','',0,'L',1);
            $pdf->Cell(125, 4,  utf8_decode('Facturas electrónicas'),'',0,'L',1);

            if ($factura_electronica[0]->factura_electronica == null) {
                $pdf->Cell(35, 4, ('0.00'),'',1,'L',1);
            }else{
                $pdf->Cell(35, 4, $factura_electronica[0]->factura_electronica,'',1,'L',1);
            }

            $pdf->Cell(10, 4, '5','',0,'L',1);
            $pdf->Cell(125, 4,  utf8_decode('Otros ingresos'),'',0,'L',1);
           
            if (($Otros_total[0]->otros) == null) {
                $pdf->Cell(35, 4, ('0.00'),'',1,'L',1);
            }else{
                $pdf->Cell(35, 4, $Otros_total[0]->otros,'',1,'L',1);
               
            }

            $pdf->SetTextColor(27, 38, 49 );
            $pdf->SetFillColor(229, 232, 232 );
            $pdf->Cell(135, 5, 'Total','',0,'L',1);
            $pdf->Cell(35, 5, $monto_total,'',1,'L',1);
          
            $pdf->Ln(10);

            //TABLA DE OTROS INGRESOS DETALLADO
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(170, 4, utf8_decode('Otros ingresos'),'B',1,'C');
            $pdf->Ln(0.6);

            // Imprimimos los encabezados de la primera tabla
            $pdf->SetFont('Arial','B',11);
            $pdf->SetTextColor(77, 86, 86 );
            $pdf->Cell(10, 4, '#','B',0,'L',0);
            $pdf->Cell(125, 4, ('Concepto'),'B',0,'L',0);
            $pdf->Cell(35, 4, ('Total'),'B',1,'L',0);
            
             //Cuerpo de la tabla
            $pdf->Ln(0.6);
            $pdf->SetTextColor(27, 38, 49 );
            $pdf->SetFillColor(248, 249, 249 ); 
            $pdf->SetFont('Helvetica','',10);
            $total_otros_ingresos_detallado=0;
            if ($otros_ingresos_detallado->count() > 0) {
                
                for ($i=0; $i < $otros_ingresos_detallado->count(); $i++) { 
                    $pdf->Cell(10, 4, ($i+1),'',0,'L',1);
                    $pdf->Cell(125, 4, utf8_decode($otros_ingresos_detallado[$i]->descripcion), '', 0, 'L', 1);
                    $pdf->Cell(35, 4, utf8_decode($otros_ingresos_detallado[$i]->monto),'', 0, 'L',1);
                    $pdf->Ln();
                    $total_otros_ingresos_detallado = $total_otros_ingresos_detallado + $otros_ingresos_detallado[$i]->monto;
                   
                }
                $pdf->SetTextColor(27, 38, 49 );
                $pdf->SetFillColor(229, 232, 232 );
                $pdf->Cell(135, 4, 'Total','',0,'L',1);
                $pdf->Cell(35, 4, $total_otros_ingresos_detallado,'',1,'L',1);
               
            }else{
                $pdf->SetTextColor(27, 38, 49 );
                $pdf->SetFillColor(229, 232, 232 );
                $pdf->Cell(170, 4, utf8_decode('No se realizaron gastos'), '', 0, 'C', 1);

            }

            $pdf->Ln(10);
            
            //TABLA DE GASTOS
            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(170, 4, utf8_decode('Gastos'),'B',1,'C');
            $pdf->Ln(0.6);

            // Imprimimos los encabezados de la primera tabla
            $pdf->SetFont('Arial','B',11);
            $pdf->SetTextColor(77, 86, 86 );
            $pdf->Cell(10, 4, '#','B',0,'L',0);
            $pdf->Cell(125, 4, ('Concepto'),'B',0,'L',0);
            $pdf->Cell(35, 4, ('Total'),'B',1,'L',0);
            
             //Cuerpo de la tabla
            $pdf->Ln(0.6);
            $pdf->SetTextColor(27, 38, 49 );
            $pdf->SetFillColor(248, 249, 249 ); 
            $pdf->SetFont('Helvetica','',10);
            $total_egresos=0;
            if ($egresos->count() > 0) {
                
                for ($i=0; $i < $egresos->count(); $i++) { 
                    $pdf->Cell(10, 4, ($i+1),'',0,'L',1);
                    $pdf->Cell(125, 4, utf8_decode($egresos[$i]->descripcion), '', 0, 'L', 1);
                    $pdf->Cell(35, 4, utf8_decode($egresos[$i]->egreso_monto),'', 0, 'L',1);
                    $pdf->Ln();
                    $total_egresos = $total_egresos + $egresos[$i]->egreso_monto;
                   
                }
                $pdf->SetTextColor(27, 38, 49 );
                $pdf->SetFillColor(229, 232, 232 );
                $pdf->Cell(135, 4, 'Total','',0,'L',1);
                $pdf->Cell(35, 4, $total_egresos,'',1,'L',1);
               
            }else{
                $pdf->SetTextColor(27, 38, 49 );
                $pdf->SetFillColor(229, 232, 232 );
                $pdf->Cell(170, 4, utf8_decode('No se realizaron gastos'), '', 0, 'C', 1);

            } 
            $pdf->Ln(15);

            $pdf->SetFont('Helvetica','B',12);
            $pdf->Cell(170, 5, utf8_decode('Cobro de cuotas de crédito'),'B',1,'C');
            $pdf->Ln(0.6);
            $pdf->SetFont('Arial','B',10);
            $pdf->SetTextColor(77, 86, 86 );
            $pdf->Cell(130, 4, utf8_decode('Cobrador'),'B',0,'L',0);
            $pdf->Cell(40, 4, utf8_decode('Total'),'B',1,'L',0);
            
            $pdf->Ln(0.6);
            
            if($pago_cuota->count() != 0){
                
                for ($i=0; $i < $cobro_cuotas->count(); $i++) {
                    $pdf->SetTextColor(27, 38, 49 );
                    $pdf->SetFillColor(248, 249, 249 ); 
                    $pdf->SetFont('Helvetica','',9);
                    $pdf->Cell(130, 4, utf8_decode($cobro_cuotas[$i]->nombre),'',0,'L',1);
                    $pdf->Cell(40, 4, $cobro_cuotas[$i]->total_cobros,'',1,'L',1);

                    
                }
                $pdf->SetTextColor(27, 38, 49 );
                $pdf->SetFillColor(229, 232, 232 );
                $pdf->Cell(130, 4, 'Total','',0,'L',1);
                $pdf->Cell(40, 4, $pago_cuota[0]->pago_cuota,'',1,'L',1);
            }

            $pdf->Ln(10);
            //var_dump($monto_total, $total_egresos); die;
            $total_ingresos_efectivo = $monto_total - $total_egresos;
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor( 248, 249, 249 );
            $pdf->Cell(130, 5, 'Total de ingresos en efectivo', 1, 0, 'C', 1);
            $pdf->Cell(40, 5, $total_ingresos_efectivo, 1, 0, 'C',1);

            $pdf->Ln(35);
                # Se ha iniciado una nueva página automáticamente, agregar contenido personalizado
                
                $pdf->SetFont('Arial','B',11);
                $pdf->Cell(65, 0, '', 1,0, 'L');
                $pdf->Cell(40, 0, '', 0);
                $pdf->Cell(65, 0, '', 1,0, 'C',1);
                $pdf->Ln();
                $pdf->Cell(65, 10, utf8_decode('Entregué  conforme - Responsable'), 0, 0, 'L');
                $pdf->Cell(55, 0, '', 0);
                $pdf->Cell(50, 10, utf8_decode('Recibí  Conforme '), 0, 0, 'R');
                $pdf->Ln();
                $pdf->SetFont('Arial','',11);
                $pdf->Cell(90, 0, utf8_decode($cajas->name), 0, 0, 'L');
                $pdf->Cell(20, 0, '', 0);
                $pdf->Cell(60, 0, 'Francisco Tarrillo Chasnamote', 0, 0, 'R');
                $pdf->Ln(20);
               
                
            
           

            // Firma del Gerente
            
            $pdf->SetFont('Arial','B',11);
            
           /*  $pdf->Ln();
            $pdf->SetXY(130, 95);
            $pdf->SetFont('Arial','',11);
            
            $pdf->SetXY(129, 85);
            $pdf->Cell(60, 0, '', 'B', 1, 'D'); */



          
        //var_dump($idsede); die;

        $pdf->Ln(1);
          
         
          
        // PRODUCTOS
        $pdf->SetFont('Helvetica', '', 8);

        ob_get_clean();
        $pdf->Output('recibo.pdf','I');
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
