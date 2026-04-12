<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Guias;
use App\Detalle_Guias;
use App\Almacen;
use App\Clientes;
use App\Productos;
use App\Sede;
use App\Ubigeo;
use App\Conductor;
use App\Vehiculo;
use Mpdf\Mpdf;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Auth;
use App\Tipo_comprobantes;
use App\Proveedor;
use App\Http\Controllers\servicios\FuncionesController;
use App\Transportista;

class GuiasController extends Controller
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
        return view('guias.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //   $idsede = session('key')->sede_id;
        $idsede = session('key')->sede_id;

        $origen=DB::table('stock_location as sl')
        ->join('almacenes as a','a.id','sl.almacen_id')
        ->select('a.id as id_almacen','sl.id','a.abreviatura','sl.name as ubicacion')
        ->where('a.sede_id','=',$idsede)
        ->get();

        $sedes=DB::table('sedes as s')->where('s.id','<>', $idsede)
        ->where('s.id','<>',1)
        ->get();

        $tipo_comprobante=Tipo_comprobantes::all();
        $proveedor=Proveedor::where('estado','=',1)->get();
        $vehiculos=Vehiculo::where('estado','=',1)->get();
        $transporte=Transportista::where('estado','=',1)->get();


        return view('guias.create',compact('origen','sedes','tipo_comprobante','proveedor','vehiculos','transporte'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        //
        DB::beginTransaction();
        try{
        $resultado=$request->all();

        //return response()->json( $resultado);

        if (!isset($resultado[1])) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "No hay ningún producto a trasladar, por favor agregue al menos un producto"
            );

            return response()->json($json);
        }

        $user = Auth::user();
        $serviciodetallealmacen=new FuncionesController;
        if($resultado[0]["tipo_traslado"]==7){

            $serie_num =$serviciodetallealmacen->correlativos($resultado[0]["tipo_traslado"]);

            $serie = $serie_num->serie;
            $numero = $serie_num->correlativo;

        }

        $idsede = session('key')->sede_id;
        $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();

        $guia=new Guias;
        $guia->numero_guia=$resultado[0]["numero_guia"];
        $guia->fecha_emision=$resultado[0]["fecha_emision"];
        $guia->documento_referencia=$resultado[0]["numero_referencia"];
        $guia->modalidad_traslado=$resultado[0]["modalidad_traslado"];
        $guia->peso_bruto=$resultado[0]["peso_bruto"];
        $guia->bultos=$resultado[0]["bultos"];
        $guia->motivo=$resultado[0]["motivo"];
        $guia->fecha_traslado=$resultado[0]["fecha"];
        $guia->fecha_recibido=Date('Y-m-d');
        $guia->hora_recibido=date("H:i:s");
        $guia->direccion_partida=$resultado[0]["direccion_partida"];
        $guia->ubigeo_partida=$resultado[0]["ubigeo_partida"];
        $guia->direccion_llegada=$resultado[0]["direccion_llegada"];
        $guia->ubigeo_llegada=$resultado[0]["ubigeo_llegada"];
        $guia->tipo_traslado_id=$resultado[0]["tipo_traslado"];
        $guia->tipo_envio = $tipo->tipo_envio;
        $guia->proveedor_id=$resultado[0]["proveedor_id"];
        $guia->transporte_id=$resultado[0]["transporte_id"];
        $guia->vehiculo_id=$resultado[0]["vehiculo_id"];
        $guia->usuario_id=$user->id;
        $guia->id_ubicacion_destino=$resultado[0]["almacen_origen"];
        $guia->tipo_documento_id=$resultado[0]["tipo_documento_id"];
        $guia->sede_id=$idsede;
        $guia->cliente_id=$resultado[0]["cliente_id"];
        $guia->estado=1;
        $guia->save();

        //return response()->json($guia);


        $descripcion="";

        $descripcion='RECEPCION DE MERCADERIA DESDE '. $guia->direccion_llegada .' A '. $guia->direccion_llegada;
        $descontar=0;

        for ($i=1; $i <count($resultado) ; $i++) {

            $detalle = new Detalle_Guias;
           // $respuestauno=$serviciodetallealmacen->detalle_alamcen_producto($resultado[$i]["cantidad"],$tipo->tipo_envio,$resultado[$i]["producto_id"],0,$this->almacen_guardar($resultado[0]["almacen_origen"],"Transferencias"));
            $respuestados= $serviciodetallealmacen->movimiento_kardex_producto($resultado[0]["almacen_origen"],$resultado[$i]["producto_id"],$resultado[$i]["cantidad"],2,$descripcion,$guia->serie ,$guia->correlativo,$this->validar_soles($resultado[$i]["producto_id"]),$guia->tipo_traslado_id,$guia->fecha,$guia->fecha);
           
           

                $descontar=$serviciodetallealmacen->aumentar_descontar_stock(1,$resultado[0]["almacen_origen"],$resultado[$i]["producto_id"],$resultado[$i]["cantidad"],$tipo->tipo_envio);
          
            $detalle->producto_id =$resultado[$i]["producto_id"];
            $detalle->guia_detalle_id =$guia->id;
            $detalle->cantidad = $resultado[$i]["cantidad"];
            $detalle->estado=1;
            $detalle->save();

            //return response()->json($this->almacen_guardar($resultado[0]["almacen_origen"]));
        }

        DB::commit();
        
        return response()->json($guia->id);

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
