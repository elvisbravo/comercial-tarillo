<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Moneda;
use App\Proveedor;
use DB;
use App\Almacen;
use App\Tipo_pago;
use App\Tipo_comprobantes;
use App\Forma_pago;
use App\Detalle_compra;
use App\Compra;
use Illuminate\Support\Facades\Auth;
use App\Detalle_almacen_productos;
use App\Http\Controllers\servicios\FuncionesController;
use PhpParser\Node\Expr\Print_;
use App\Unidad_medidas;
use App\Productos;

class ComprasController extends Controller
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

        return view('compras.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //METODO PARA LISTAR LAS COMPRAS
     public function listacompras(){

        $idsede = session('key')->sede_id;
        $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();

         $compras=DB::table('compras as c')
         ->join('proveedors as pr','c.proveedor_id','=','pr.id')
         ->join('stock_location as sl','c.ubicacion_id','=','sl.id')
         ->join('almacenes as a','sl.almacen_id','=','a.id')
         ->join('monedas as m','c.moneda_id','=','m.id')
         ->leftjoin('forma_pagos as fp','c.forma_pago_id','=','fp.id')
         ->join('tipo_pagos as  tp','c.tipo_pago_id','=','tp.id')
         ->join('tipo_comprobantes as tc','c.tipo_comprobante_id','=','tc.id')
         ->select('c.id','pr.nombre_comercial','c.serie_comprobante','c.correlativo_comprobante','c.total_compra','fp.descripcion','c.fecha_compra','c.estado')
         ->where('c.sede_id','=',$idsede)
         ->get();


         return response()->json($compras);
     }

    //METODO PATA TRAER LOS DATOS SEGUN TOP
    public function topoproductos($text){

        $idsede = session('key')->sede_id;

        if($text==0){
            $productos=DB::table('productos as p')
            ->leftjoin('categorias as c','p.categoria_id','=','c.id')
            ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
            ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
            ->leftjoin('marcas as m','p.marca_id','=','m.id')
            ->leftjoin('colores as co','p.color_id','=','co.id')
            ->select('p.id','p.nomb_pro','p.prec_compra','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
             'm.descripcion as marca','m.id as idmarca','p.descuento_mini_venta_cre','p.precio_venta_contado','p.precio_venta_credito','co.descripcion as color','co.id as idcolores','p.img')
             ->where('p.sede_id','=',$idsede)
            ->get();

        }else{
            $productos=DB::table('productos as p')
            ->leftjoin('categorias as c','p.categoria_id','=','c.id')
            ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
            ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
            ->leftjoin('marcas as m','p.marca_id','=','m.id')
            ->leftjoin('colores as co','p.color_id','=','co.id')
            ->select('p.id','p.nomb_pro','p.prec_compra','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
             'm.descripcion as marca','m.id as idmarca','p.descuento_mini_venta_cre','p.precio_venta_contado','p.precio_venta_credito','co.descripcion as color','co.id as idcolores','p.img')
             ->where('p.sede_id','=',$idsede)
             ->where('p.nomb_pro','like','%'.$text.'%')
            ->get();


        }





        return response()->json($productos);


    }
    public function create(Request $request)
    {
        $usuario = $request->session()->get('key');
        $monedas=Moneda::all();

        if($usuario->sede_id==1){

            $almacenes=Almacen::where('sede_id','<>',1)->get();

        }else{

           // $almacenes=Almacen::where('sede_id','=',$usuario->sede_id)->get();

            $almacenes=DB::table('stock_location as sl')
            ->join('almacenes as a','a.id','sl.almacen_id')
            ->select('a.id as id_almacen','sl.id','a.abreviatura','sl.name as ubicacion')
            ->where('a.sede_id','=',$usuario->sede_id)
            ->where('sl.name','=','Stock')
            ->get();
        }

        //print_r($usuario->sede_id);exit();

        ///$almacenes=Almacen::where('sede_id','=',$usuario->sede_id)->get();

        //print_r($almacen);exit();
        $proveedores=Proveedor::where('estado','=',1)->select('id','nombre_comercial')->get();
        $tipopago=Tipo_pago::all();
        //print_r($tipopago);exit();
        $comprobante=Tipo_comprobantes::whereIn('id', [1, 2,12])->get();

        $formapago=Forma_pago::all();

        //print_r($formapago);exit();

        return view('compras.create',compact('monedas','proveedores','almacenes','tipopago','comprobante','formapago'));
    }

    //LISTAR LAS UNIDADES DE MEDIDA
    public function unidades(){

          $unidades=Unidad_medidas::all();
          return response()->json($unidades);

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

            $respuestauno="";
            $respuestados="";

            $resultado=$request->all();
            $user = Auth::user();
            $idsede = session('key')->sede_id;
            $serie='';
            $numero='';
            $serviciodetallealmacen=new FuncionesController;

      
            $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();
            if($resultado[0]["tipo_comprobante_id"]==12){

                $serie_num =$serviciodetallealmacen->correlativos($resultado[0]["tipo_comprobante_id"]); 
                $serie = $serie_num->serie;
                $numero = $serie_num->correlativo;

            }else{
                $serie = $resultado[0]["serie_comprobante"];
                $numero = $resultado[0]["correlativo_comprobante"];


            }
           

            $flete_total=0;

            for($i=1; $i <count($resultado) ; $i++){
                $flete_total=+$flete_total+$resultado[$i]["flete"];

            }

           $compra=new Compra;
           $compra->proveedor_id=$resultado[0]["proveedor_id"];
           $compra->ubicacion_id=$resultado[0]["almacen_id"];
           $compra->moneda_id=$resultado[0]["moneda_id"];
           $compra->user_id=$user->id;
          // $compra->forma_pago_id=$resultado[0]["forma_pago_id"];
           $compra->tipo_pago_id=$resultado[0]["tipo_pago_id"];
           $compra->tipo_comprobante_id=$resultado[0]["tipo_comprobante_id"];
           $compra->fecha_ingreso=date('Y-m-d');
           $compra->fecha_compra=$resultado[0]["fecha_compra"];
           $compra->serie_comprobante=$serie;
           $compra->correlativo_comprobante= $numero ;
           $compra->compra_venta=$resultado[0]["compra_venta"];
           $compra->total_igv=$resultado[0]["total_igv"];
           $compra->total_compra=$resultado[0]["total_compras"];
           $compra->cambio_monto=$resultado[0]["cambio_monto"];
           $compra->porcentaje_igv=$resultado[0]["porcentaje_igv"];
           $compra->total_compra_flete=$flete_total+$resultado[0]["total_compras"];
           $compra->sede_id=$idsede;
           $compra->tipo=$tipo->tipo_envio;
           $compra->estado=1;
           $compra->save();

           //return response()->json($compra->total_compra_flete);

          // print('');exit();




            $descripcion="";
            //if($resultado[0]["tipo_comprobante_id"]==1){

                $descripcion='COMPRA '.$compra->serie_comprobante.'-'.$compra->correlativo_comprobante;

            //}else if($resultado[0]["tipo_comprobante_id"]==2){

                //$descripcion='FACTURA ELECTRONICA '.$compra->serie_comprobante.'-'.$compra->correlativo_comprobante;

            //}
           
            $promedio=0;

           for ($i=1; $i <count($resultado) ; $i++) {

                $detalle=new Detalle_compra;
               

                //$respuestauno=$serviciodetallealmacen->detalle_alamcen_producto($resultado[$i]["cantidad"],0,$resultado[$i]["producto_id"],$tipo->tipo_envio,$this->almacen_guardar($resultado[0]["almacen_id"]));
                $sumar= $serviciodetallealmacen->aumentar_descontar_stock(1,$resultado[0]["almacen_id"],$resultado[$i]["producto_id"],$resultado[$i]["cantidad"],$tipo->tipo_envio);
                $respuestados= $serviciodetallealmacen->movimiento_kardex_producto($resultado[0]["almacen_id"],$resultado[$i]["producto_id"],$resultado[$i]["cantidad"],1,$descripcion,$compra->serie_comprobante,$compra->correlativo_comprobante,$resultado[$i]["precio"],$resultado[0]["tipo_comprobante_id"],$compra->fecha_ingreso,$resultado[0]["fecha_compra"]);

                $detalle->compra_id=$compra->id;
                $detalle->producto_id=$resultado[$i]["producto_id"];
                $promedio=$this->actualizar_promedio($detalle->producto_id,$resultado[$i]["precio"]);


                

                $detalle->unidad_medida_id=$resultado[$i]["unidad_medida_id"];
                $detalle->cantidad=$resultado[$i]["cantidad"];
                $detalle->precio=$resultado[$i]["precio"];
                $detalle->subtotal=$resultado[$i]["subtotal"];
                $detalle->flete=$resultado[$i]["flete"];
                $detalle->costo_flete=$resultado[$i]["subtotal"]+$resultado[$i]["flete"];
                $detalle->save();
                   // return response()->json($respuestados);

               // print('');exit();

           }

           DB::commit();

            return response()->json($promedio);


        }catch (Exception $e) {

            return  response()->json($e);

        }

    }

    //FUNCION PARA ACTUALIZAR EL PRECIO PROMEDIO DE UN PRODUCTOS
    public function actualizar_promedio($codigo,$precio){

        $detalle=DB::table('detalle_compras as dt')
        ->select('dt.precio')
        ->where('dt.producto_id','=',$codigo)
        ->get();

        $total = 0;

        foreach($detalle as $det){

            $total = $det->precio + $total;

        }

        if(count($detalle)==0){

            $average = $precio / 1;

        }else{

            $average = $total / count($detalle);

        }

       

        $productos=Productos::find($codigo);
        $productos->costo= $average;
        $productos->save();

        return 'ok';

    }
    //DATOS PARA TRAER LOS ALMACENES
    public function almacen_guardar($almacen_id){

        $almacen=DB::table('almacenes as a')
        ->join('stock_location as s','s.almacen_id','=','a.id')
        ->select('s.id')
        ->where('s.almacen_id','=',$almacen_id)
        ->where('s.name','=','Stock')
        ->first();

        return $almacen->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ver($id)
    {

        $detalle=DB::table('detalle_compras as dt')
        ->join('productos as p','dt.producto_id','=','p.id')
        ->join('unidad_medidas as u','dt.unidad_medida_id','=','u.id')
        ->select('p.nomb_pro','u.descripcion','dt.cantidad','dt.precio','dt.subtotal')
        ->where('dt.compra_id','=',$id)
        ->get();

        return response()->json( $detalle);

    }
    
    //METODO PARA ANALIZAR EL COSTO PROMEDIO DE UN PRODUCTO



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        DB::beginTransaction();

        try{
        $user = Auth::user();
        $idsede = session('key')->sede_id;
        

        $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();

        if (!is_numeric($id)) {
           

            $json = array(
                "respuesta" => "error",
                "mensaje" => "El ID de la compra debe ser un número"
            );
    
            return response()->json($json);
        }

       

        $detalle = $this->obtenerDetalleCompra($id);

        $verificador=$this->validar_anulacion($id);

        if( $verificador==1){

            $json = array(
                "respuesta" => "error",
                "mensaje" => "Ya no podemos anular la compra, el sistema detecto que los productos ya tuvieron movimientos"
            );
    
            return response()->json($json);

        }else{

            $compra = $this->obtenerCompra($id);

           
    
            if (!$compra) {
               
                $json = array(
                    "respuesta" => "error",
                    "mensaje" => "No se encontró la compra con el ID especificado"
                );
        
                return response()->json($json);
            }

           


            $compra->estado=0;
            
            $compra->save();
           // return response()->json($compra);

            foreach($detalle as $dt){
                
                $serviciodetallealmacen=new FuncionesController;
                
                $sumar= $serviciodetallealmacen->aumentar_descontar_stock(0,$dt->ubicacion_id,$dt->producto_id,$dt->cantidad,$tipo->tipo_envio);

            }


        }
      


       

        $json = array(
            "respuesta" => "ok",
            "mensaje" => "Compra Anulada Correctamente"
        );
        DB::commit();

        return response()->json($json);

        }catch (Exception $e) {

            return  response()->json($e);

        }

        

    }

    //METODO PARA VALIDAR TODO EL ARRAY DE LA COMPRA Y SI ME REGRESA 1 ES PORQUE NO DEBO MODIFICAR  Y 0 SI DEVO MODIFICAR
    public function validar_anulacion($id){

        $detalle = $this->obtenerDetalleCompra($id);


        $valor=0;

        foreach($detalle as $dt){

            $valor=$this->validar_stock($dt->producto_id,$dt->ubicacion_id,$dt->cantidad);

            if($valor==1){
                $valor=0;
            }else{
                $valor=1;
            }
        }

        return $valor;

    }


    //metodo para comprobar si el monto ingresado es igual al del stock
    public function validar_stock($id,$ubicacion,$cantidad){

        $detalle=DB::table('detalle_almacen_productos as dt')
        ->where('dt.producto_id','=',$id)
        ->where('dt.ubicacion_id','=',$ubicacion)
        ->first();

        $valor=0;
        $suma=($detalle->stock-$cantidad);

        if($suma==0){
          $valor=1;
        }


        return $valor;

    }

    public function obtenerCompra($id){
            return Compra::find($id);
        }

        public function obtenerDetalleCompra($compraId){

            return DB::table('compras as c')
                ->join('detalle_compras as dt', 'dt.compra_id', '=', 'c.id')
                ->where('dt.compra_id', '=', $compraId)
                ->get();
        }
}
