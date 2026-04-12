<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Almacen;
use App\Clientes;
use App\Productos;
use App\Traslado;
use App\Detalle_traslado;
use App\Sede;
use App\Ubigeo;
use App\Conductor;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Http\Controllers\servicios\FuncionesController;



class RecepcionGuiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */   public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
        $idsede = session('key')->sede_id;
        $sede=Sede::where('id','=',$idsede)->first();

        return view('recepcion-mercaderia.index',compact('sede'));
    }

    public function listadoguiasrecepcion(){

        $idsede = session('key')->sede_id;

        $guias=DB::table('traslados as t')
        ->join('clientes as c','t.cliente_id','=','c.id')
        ->select('t.id','t.fecha','t.serie','t.correlativo','t.estado','t.motivo','c.razon_social')
        ->where('t.sede_destino','=', $idsede)
        ->get();

        return response()->json($guias);

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
    public function save(Request $request)
    {
        //
        DB::beginTransaction();

        try{

            $serviciodetallealmacen=new FuncionesController;

            $idsede = session('key')->sede_id;
            $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();

            //MODIFICAR EL DETALLE

           

            $descripcion='RECEPCION DE MERCADERIA DESDE '. $request->direccion_llegada .' A '. $request->direccion_llegada;

            if($request->cantidad_recibido==""){

                $json = array(
                    "respuesta" => "error",
                    "mensaje" => "La cantidad Realizada es requerida"
                );
    
                return response()->json($json);
            }

            
            if($request->ubicacion_id==""){

                $json = array(
                    "respuesta" => "error",
                    "mensaje" => "Es necesario Presisar el origen donde ingresara la Mercaderia"
                );
    
                return response()->json($json);
            }




            $detalle=Detalle_traslado::find($request->id);

            

            if($detalle->diferencia!=0){

                $detalle->cantidad_recibido=$detalle->cantidad_recibido+$request->cantidad_recibido;
                $detalle->diferencia=($detalle->cantidad-$detalle->cantidad_recibido);

            }else if($detalle->diferencia===0){

                //return response()->json('hola');


                $json = array(
                    "respuesta" => "error",
                    "mensaje" => "El sistema detecto que ya fue recepcionada la mercaderia en su totalidad"
                );
    
                return response()->json($json);

            
            }else {

               

                $detalle->cantidad_recibido=$request->cantidad_recibido;
                $detalle->diferencia=($detalle->cantidad-$request->cantidad_recibido);

            }
            
            
       
           // $detalle->id_ubicacion_destino=$request->ubicacion_id;



            if($detalle->diferencia<0){

                $json = array(
                    "respuesta" => "error",
                    "mensaje" => "La Cantidad Recibida no puede ser mayor a la cantidad demandada"
                );
    
                return response()->json($json);

            }else if($detalle->diferencia==0){
                $detalle->estado=0;
            }else{
                $detalle->estado=2;
            }
            //$detalle->save();

            $ubicacion_enviada=$serviciodetallealmacen->ubicacion_almacen_interno($request->id_almacen_origen,'Transferencias');
            $ubicacion_entrada=$request->ubicacion_id;

           
           

           //$respuestauno=$serviciodetallealmacen->detalle_alamcen_producto($request->cantidad_recibido,$tipo->tipo_envio,$resultado[$i]["producto_id"],0,$this->almacen_guardar($resultado[0]["almacen_origen"],"Transferencias"));

            //$menos= $serviciodetallealmacen->aumentar_descontar_stock(0,$ubicacion_enviada,$request->id_producto,$request->cantidad_recibido,$tipo->tipo_envio);

           
            $sumar= $serviciodetallealmacen->aumentar_descontar_stock(1,$ubicacion_entrada,$request->id_producto,$request->cantidad_recibido,$tipo->tipo_envio);

           

            $respuestados= $serviciodetallealmacen->movimiento_kardex_producto($ubicacion_entrada,$request->id_producto,$request->cantidad_recibido,1,$descripcion,$request->serie ,$request->correlativo,$this->validar_soles($request->id_producto),$request->tipo_traslado_id,date('Y-m-d'),$request->fecha);
            
           
           
          
           $detalle->save();

           

           


            DB::commit();
           //\Log::debug($detalle);

           $json = array(
            "respuesta" => "ok",
            "mensaje" => $detalle->traslado_id
            );

            return response()->json($json);


        }catch (Exception $e) {

            return  response()->json($e);
    
        }
    }
    //FUNCION PARA VALIDAR TODA LA DATA DE LA GUIA
    public function guardar($id,$id_ubicacion_destino){

        DB::beginTransaction();

        try{
        $user = Auth::user();
             
        $cabecera=Traslado::find($id);

        $detalle = DB::table('detalle_traslado')
        ->join('productos','productos.id','=','detalle_traslado.producto_id')
        ->where('detalle_traslado.traslado_id',$id)
        ->get();

        $valor=0;

        foreach ($detalle as $key => $value) {
                  
             $diferencia=($value->cantidad-$value->cantidad_recibido);

             if($diferencia!=0){
                $valor=1;
                
             }

        }

        if($valor==0){
            $cabecera->estado=0;
        }else{

            $cabecera->estado=2;
        }
        $cabecera->fecha_recibido=date('Y-m-d');
        $cabecera->user_recepcion=$user->id;
        $cabecera->hora_recibido= date("H:i:s");
        $cabecera->id_ubicacion_destino=$id_ubicacion_destino;
        $cabecera->save();


        DB::commit();
        return response()->json('OK');

    }catch (Exception $e) {

        return  response()->json($e);

    }

      

    }

     //traer el precio de venta de la lista de precios
     public function validar_soles($codigo){

        $precio_venta=DB::table('precios as p')
        ->where('p.articulo_id','=',$codigo)
        ->first();

        

        return $precio_venta->precio_contado;

    }

     //METODO PARA PODER CARGAR EL DETALLE
     public function detalle($id){

       

        $detalle = DB::table('detalle_traslado as dt')
        ->join('productos as p','p.id','=','dt.producto_id')
        ->join('unidad_medidas as m','m.id','p.unidad_medida_id')
        ->select('dt.id','dt.producto_id','p.nomb_pro','dt.cantidad','dt.cantidad_recibido','dt.diferencia','m.descripcion')
        ->where('dt.traslado_id',$id)
        ->get();
        return response()->json($detalle);
        



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
        $idsede = session('key')->sede_id;

        $guias=DB::table('traslados as t')
        ->join('clientes as c','t.cliente_id','=','c.id')
        ->join('conductor as ct','t.conductor_id','=','ct.id')
        ->leftjoin('vehiculo as v','t.vehiculo_id','=','v.id')
        ->join('ubigeos as u','t.ubigeo_partida','u.id')
        ->join('ubigeos as ud','t.ubigeo_llegada','ud.id')
        ->join('almacenes as al','t.almacen_origen','al.id')
        ->join('almacenes as ald','t.almacen_destino','ald.id')
        ->select('t.id','t.fecha','t.serie','t.correlativo','t.estado','t.motivo','c.razon_social','t.modalidad_traslado','t.peso_bruto',
        't.bultos','ct.nombre as conductor','ct.numero_documento as documento','v.placa','t.direccion_partida','t.direccion_llegada',
          'u.departamento','u.provincia','u.distrito','ud.departamento as dep','ud.provincia as pro','ud.distrito as dist',
           'al.nombre as origen','ald.nombre as destino','t.estado','al.id as id_almacen_origen','t.tipo_traslado_id','t.id_ubicacion_origen','t.id_ubicacion_destino')
        ->where('t.id','=', $id)
        ->first();
        //print_r($guias);exit();

        $detalle = DB::table('detalle_traslado')
        ->join('productos','productos.id','=','detalle_traslado.producto_id')

        ->where('detalle_traslado.traslado_id',$id)
        ->get();

        //METODO PARA TRAER TODAS LAS UBICACIONES DEL ALMACEN ACTUAL
        $ubicaciones=DB::table('stock_location as sl')
        ->join('almacenes as a','a.id','sl.almacen_id')
        ->select('a.id as id_almacen','sl.id','a.abreviatura','sl.name as ubicacion')
        ->where('a.sede_id','=',$idsede)
        ->get();
        $id_guia=$id;
        


        return view('recepcion-mercaderia.show',compact('guias','detalle','ubicaciones','id_guia'));
    }

   

    //METODO PARA PODER TRAER DEL ARTICULO
    public function articulo_demandado($product_id,$traslado_id){

        $detalle = DB::table('detalle_traslado as dt')
        ->join('productos as p','p.id','=','dt.producto_id')
        ->select('dt.id','dt.producto_id','p.nomb_pro','dt.cantidad','dt.cantidad_recibido','dt.diferencia')
        ->where('dt.producto_id',$product_id)
        ->where('dt.traslado_id','=',$traslado_id)
        ->first();

        return response()->json( $detalle);

    }

    //METODO PARA PODER GUARDAR LA DATA POR CADA DETALLE RECIBIDO

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
