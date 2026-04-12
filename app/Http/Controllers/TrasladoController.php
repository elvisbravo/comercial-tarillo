<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Almacen;
use App\Clientes;
use App\Productos;
use App\Traslado;
use App\Detalle_traslado;
use App\Sede;
use App\Ubigeo;
use App\Conductor;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\servicios\FuncionesController;

class TrasladoController extends Controller
{
    private $fpdf;
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
        return view('traslado.index');
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $idsede = session('key')->sede_id;

        //$origen = Almacen::where('sede_id','=',$idsede)->where('estado','=',1)->get();
        //$destinos = Almacen::where('sede_id','!=', $idsede)->where('sede_id','!=',1)->where('estado','=',1)->get();

        $origen=DB::table('stock_location as sl')
        ->join('almacenes as a','a.id','sl.almacen_id')
        ->select('a.id as id_almacen','sl.id','a.abreviatura','sl.name as ubicacion')
        //->where('a.sede_id','=',$idsede)
        ->get();

        $destinos=DB::table('stock_location as sl')
        ->join('almacenes as a','a.id','sl.almacen_id')
        ->select('a.id as id_almacen','sl.id','a.abreviatura','sl.name as ubicacion')
        //->where('a.sede_id','=',$sede_id)
        ->get();

       

        $sedes=DB::table('sedes as s')->where('s.id','<>', $idsede)
        ->where('s.id','<>',1)
        ->get();


        return view('traslado.create',compact('origen','sedes','destinos'));
    }

    //TRAER LOS UBICACIONES SEGUN EL STOCK
    public function ubicaciones_stock_sede($sede_id){

        $destinos=DB::table('stock_location as sl')
        ->join('almacenes as a','a.id','sl.almacen_id')
        ->select('a.id as id_almacen','sl.id','a.abreviatura','sl.name as ubicacion')
        ->where('a.sede_id','=',$sede_id)
        ->get();


        return response()->json($destinos);


    }

    


    //METODO PARA TRAER LOS TRANSPORTISTAS POR NUMERO DE DOCUMENTO
    public function lista_conductor($documento){

             $conductor=Conductor::where('numero_documento','=',$documento)->first();

             return response()->json($conductor);
             
    }

   


    public function mostrar()
    {
        $html = "";

        $tipo_envio = new FuncionesController;

        $envio = $tipo_envio->tipo_envio_sunat();

        $idsede = session('key')->sede_id;

        $traslados = Traslado::where('tipo_envio','=',$envio)->where('sede_id','=',$idsede)->get();

        foreach ($traslados as $key => $value) {
            $origen = Almacen::find($value->almacen_origen);
            $destino = Almacen::find($value->almacen_destino);

            $html .= "<tr>";
            $html .= "<td>".($key+1)."</td>";
            $html .= "<td>".$origen->nombre."</td>";
            $html .= "<td>".$destino->nombre."</td>";
            $html .= "<td>".date('d-m-Y',strtotime($value->fecha))."</td>";
            $html .= "<td>".$value->serie."-".$value->correlativo."</td>";
            $html .= '<td>
                <a href="traslado/guia/'.$value->id.'" target="_blank" class="btn btn-warning btn-sm btn-icon">
                    <i class="fa fa-file-alt"></i>
                </a>
            </td>';
            $html .= "</tr>";


        }

        return response()->json($html);

    }


     //CONSULTA PARA PODER TRAER LOS DATOS DE LAS GUIAS GENERADAS
     public function listadoguias(){

        $idsede = session('key')->sede_id;

        $guias=DB::table('traslados as t')
        ->join('clientes as c','t.cliente_id','=','c.id')
        ->select('t.id','t.fecha','t.serie','t.correlativo','t.estado','t.motivo','c.razon_social')
        ->where('t.sede_id','=', $idsede)
        ->get();

        return response()->json($guias);

    }



    public function traer_productos()
    {
        $idsede = session('key')->sede_id;
        $tipo_envio = new FuncionesController;

        $envio = $tipo_envio->tipo_envio_sunat();

        $productos=DB::table('productos as p')
                ->leftjoin('detalle_almacen_productos as dp','dp.producto_id','=','p.id')
                ->leftjoin('stock_location as sl','dp.ubicacion_id','=','sl.id')
                ->leftjoin('almacenes as a','sl.almacen_id','=','a.id')
                ->leftjoin('precios as pr','pr.articulo_id','=','p.id')
                ->leftjoin('categorias as c','p.categoria_id','=','c.id')
                ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
                ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
                ->leftjoin('marcas as m','p.marca_id','=','m.id')
                ->leftjoin('colores as co','p.color_id','=','co.id')
                ->select('p.id','p.nomb_pro','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
                 'm.descripcion as marca','m.id as idmarca','co.descripcion as color','co.id as idcolores',DB::raw('SUM(dp.stock) as stock'),
                 'pr.precio_contado','pr.precio_credito','pr.descuento_contado','pr.descuento_credito')
                ->groupBy('p.id','p.nomb_pro','c.categoria','c.id','sub.subcategoria','sub.id','u.descripcion','u.id',
                'm.descripcion','m.id','co.descripcion','co.id','pr.precio_contado','pr.precio_credito','pr.descuento_contado','pr.descuento_credito')
                ->where('a.sede_id','=', $idsede)
                ->where('sl.name','=','Stock')
                ->where('p.estado','=','1')
                //->where('p.id','=','910')
                ->get();

        $data = array();

        foreach ($productos as $key => $value) {
            $datos = array(
                "value" => $value->id,
                "label" => $value->nomb_pro
            );

            array_push($data,$datos);
        }

        return response()->json($data);
    }

    public function render_producto($id,$ubicacion_id)
    {
        $idsede = session('key')->sede_id;
        $tipo_envio = new FuncionesController;

        $envio = $tipo_envio->tipo_envio_sunat();

        $productos=DB::table('productos as p')
                ->leftjoin('detalle_almacen_productos as dp','dp.producto_id','=','p.id')
                ->leftjoin('stock_location as sl','dp.ubicacion_id','=','sl.id')
                ->leftjoin('almacenes as a','sl.almacen_id','=','a.id')
                ->leftjoin('precios as pr','pr.articulo_id','=','p.id')
                ->leftjoin('categorias as c','p.categoria_id','=','c.id')
                ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
                ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
                ->leftjoin('marcas as m','p.marca_id','=','m.id')
                ->leftjoin('colores as co','p.color_id','=','co.id')
                ->select('p.id','p.nomb_pro','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
                 'm.descripcion as marca','m.id as idmarca','co.descripcion as color','co.id as idcolores',DB::raw('SUM(dp.stock) as stock'),
                 'pr.precio_contado','pr.precio_credito','pr.descuento_contado','pr.descuento_credito')
                ->groupBy('p.id','p.nomb_pro','c.categoria','c.id','sub.subcategoria','sub.id','u.descripcion','u.id',
                'm.descripcion','m.id','co.descripcion','co.id','pr.precio_contado','pr.precio_credito','pr.descuento_contado','pr.descuento_credito')
                ->where('a.sede_id','=', $idsede)
                ->where('p.id','=',$id)
                ->where('sl.id','=',$ubicacion_id)
                ->where('p.estado','=','1')
                ->first();

        return response()->json($productos);
    }

    public function data_producto(Request $request)
    {
        $idproducto = $request->idproducto;
        $idalmacen = $request->idalmacen;

        $tipo_envio = new FuncionesController;

        $envio = $tipo_envio->tipo_envio_sunat();

        if ($idproducto == "") {
            return response()->json("");
        }

        $productos = DB::table('productos as pro')->select('pro.id as idpro','*')->join('detalle_almacen_productos as dap','pro.id','=','dap.producto_id')->where('dap.almacen_id','=',$idalmacen)->where('dap.producto_id','=',$idproducto)->where('dap.tipo_envio','=',$envio)->first();

        return response()->json($productos);
    }

    public function guardar(Request $request)
    {
        DB::beginTransaction();

         try{
            
          $resultado=$request->all();

         

       if (!isset($resultado[1])) {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "No hay ningún producto a trasladar, por favor agregue al menos un producto"
            );

            return response()->json($json);
        }

       
        $user = Auth::user();
        $serviciodetallealmacen=new FuncionesController;

        $serie_num =$serviciodetallealmacen->correlativos($resultado[0]["tipo_traslado"]); //$this->correlativos($request->doc_traslado);

        $serie = $serie_num->serie;
        $numero = $serie_num->correlativo;

        $fecha = date('Y-m-d');
        $idsede = session('key')->sede_id;
        $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();

       
       $traslado = new Traslado;
       $traslado->tipo_traslado_id=$resultado[0]["tipo_traslado"];
       $traslado->cliente_id=$resultado[0]["cliente_id"];
       $traslado->email=$resultado[0]["email"];
       $traslado->motivo =$resultado[0]["motivo"];
       $traslado->modalidad_traslado =$resultado[0]["modalidad_traslado"];
       $traslado->fecha = $resultado[0]["fecha"];
       $traslado->peso_bruto = $resultado[0]["peso_bruto"];
       $traslado->bultos = $resultado[0]["bultos"];
       $traslado->conductor_id = $resultado[0]["conductor_id"];
       $traslado->direccion_partida = $resultado[0]["direccion_partida"];
       $traslado->ubigeo_partida = $resultado[0]["ubigeo_partida"];
       $traslado->direccion_llegada = $resultado[0]["direccion_llegada"];
       $traslado->ubigeo_llegada = $resultado[0]["ubigeo_llegada"];
       $traslado->almacen_origen =$this->almacen_guardar($resultado[0]["almacen_origen"]); //$resultado[0]["almacen_origen"];
       $traslado->almacen_destino =$this->almacen_guardar($resultado[0]["almacen_destino"]); //$resultado[0]["almacen_destino"];
       $traslado->id_documento_electronico = $resultado[0]["id_documento_electronico"];
       $traslado->serie = $serie;
       $traslado->correlativo = $numero;
       $traslado->estado = 1;
       $traslado->tipo_envio = $tipo->tipo_envio;
       $traslado->sede_id = $idsede;
       $traslado->id_ubicacion_origen=$resultado[0]["almacen_origen"];
       $traslado->hora= date("H:i:s");
       $traslado->sede_destino=$this->sede_destino($resultado[0]["almacen_destino"]);
       $traslado->user_id=$user->id;
       $traslado->save();

      

    

        $descripcion="";

        $descripcion='TRASLADO DE MERCADERIA DESDE '. $traslado->direccion_llegada .' A '. $traslado->direccion_llegada;

     
        $descontar=0;

        for ($i=1; $i <count($resultado) ; $i++) {

            $detalle = new Detalle_traslado;
           // $respuestauno=$serviciodetallealmacen->detalle_alamcen_producto($resultado[$i]["cantidad"],$tipo->tipo_envio,$resultado[$i]["producto_id"],0,$this->almacen_guardar($resultado[0]["almacen_origen"],"Transferencias"));
            $respuestados= $serviciodetallealmacen->movimiento_kardex_producto($resultado[0]["almacen_destino"],$resultado[$i]["producto_id"],$resultado[$i]["cantidad"],2,$descripcion,$traslado->serie ,$traslado->correlativo,$this->validar_soles($resultado[$i]["producto_id"]),$traslado->tipo_traslado_id,$traslado->fecha,$traslado->fecha);
           
            if($traslado->id_documento_electronico==''){

                $descontar=$serviciodetallealmacen->aumentar_descontar_stock(0,$resultado[0]["almacen_origen"],$resultado[$i]["producto_id"],$resultado[$i]["cantidad"],$tipo->tipo_envio);
            }
            $detalle->producto_id =$resultado[$i]["producto_id"];
            $detalle->traslado_id =$traslado->id;
            $detalle->cantidad = $resultado[$i]["cantidad"];
            $detalle->estado=1;
            $detalle->save();

            //return response()->json($this->almacen_guardar($resultado[0]["almacen_origen"]));



        }

        $json = array(
            "respuesta" => "ok",
            "mensaje" => "Se guardo correctamente el traslado"
        );

        DB::commit();

        return response()->json( $descontar);

    }catch (Exception $e) {

        return  response()->json($e);

    }



    }

     //traer las ubicaciones internas
     public function almacen_guardar($almacen_id){

        $almacen=DB::table('almacenes as a')
        ->join('stock_location as s','s.almacen_id','=','a.id')
        ->select('a.id')
        ->where('s.id','=',$almacen_id)
       
        ->first();

        return $almacen->id;
    }

    //traer el precio de venta de la lista de precios
    public function validar_soles($codigo){

        $precio_venta=DB::table('precios as p')
        ->where('p.articulo_id','=',$codigo)
        ->first();


        return $precio_venta->precio_contado;

    }

    //FUNCION PARA PODER TRAER LA SEDE SEGUN LA UBICACIÓN DESTINO
    public function sede_destino($ubicacion){

        $sedes_destino=DB::table('stock_location as sl')
        ->join('almacenes as a','sl.almacen_id','=','a.id')
        ->select('a.sede_id')
        ->where('sl.id','=',$ubicacion)
        ->first();

        return $sedes_destino->sede_id;
    }


    


    public function generar_guia($id)
    {

        
        $img=base_path('public/img/logo2.jpeg');

        $idsede = session('key')->sede_id;

        $data_sede = Sede::find($idsede);

        $data_traslado = Traslado::find($id);

    

        $origen = Almacen::find($data_traslado->almacen_origen);
        $destino = Almacen::find($data_traslado->almacen_destino);
        $estdo_guia='';

        $estados = array(
            0 => 'GUIA ATENDIDO',
            1 => 'PENDIENTE DE ACEPTACIÓN',
            2 => 'ATENDIDO PARCIAL',
            3 => 'ANULADO'
        );

        $estdo_guia = $estados[$data_traslado->estado] ?? 'ESTADO DESCONOCIDO';

        $detalle = DB::table('detalle_traslado')->join('productos','productos.id','=','detalle_traslado.producto_id')->where('detalle_traslado.traslado_id',$id)->get();
       
        $this->fpdf = new Fpdf('P','mm',array(80,297));;
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('helvetica','',10);

        $this->fpdf->Image($img,25,5,30);
        $this->fpdf->Ln(10);
        $this->fpdf->MultiCell(60,4,'TARRILLO CHASNAMOTE FRANCISCO',0,'C');
        $this->fpdf->SetFont('Helvetica','',8);
        $this->fpdf->MultiCell(60,4,'MULTISERVICIOS TARRILLO',0,'C');
        $this->fpdf->MultiCell(60,4,utf8_decode('BAR. MORALILLOS CAL. FRANCISCO BARDALES'),0,'C');

        $this->fpdf->MultiCell(60,4,"SEDE: ".$data_sede->nombre,0,'C');


        $this->fpdf->MultiCell(60,4,"RUC: 10448761873",0,'C');
        $this->fpdf->Ln(2);

        $this->fpdf->SetFont('Helvetica', 'B', 8);
        $this->fpdf->MultiCell(60,4,utf8_decode("GUIA INTERNA: ".$data_traslado->serie."-".$data_traslado->correlativo),0,'C');
        $this->fpdf->SetFont('Helvetica','',8);

        $this->fpdf->Ln(5);
        $this->fpdf->MultiCell(60,4,'FECHA: '.utf8_decode(date('d-m-Y',strtotime($data_traslado->fecha))),0,'');
        $this->fpdf->MultiCell(60,4,'ORIGEN: '.utf8_decode($origen->nombre),0,'');
        $this->fpdf->MultiCell(60,4,'DESTINO: '.utf8_decode($destino->nombre),0,'');
        $this->fpdf->MultiCell(60,4,'ESTADO: '.utf8_decode($estdo_guia),0,'');

        //columnas
        $this->fpdf->SetFont('Helvetica', 'B', 8);
        $this->fpdf->Cell(38, 10, utf8_decode('Descripción'), 0);
        $this->fpdf->Cell(15, 10, 'Cantidad',0,0,'R');
        $this->fpdf->Ln(8);
        $this->fpdf->Cell(60,0,'','T');
        $this->fpdf->Ln(2);

          // PRODUCTOS
          $this->fpdf->SetFont('Helvetica', '', 8);

        foreach ($detalle as $key => $value) {
        
            $this->fpdf->MultiCell(30,4,utf8_decode($value->nomb_pro),0);
            $this->fpdf->Cell(48, -15, $value->cantidad,0,0,'R');
            $this->fpdf->Ln(2);
        }

        $this->fpdf->Output();
        exit;
    }



    public function ubigeo($id)
    {
        $ubigeo = Ubigeo::all();

        $data = [];

        foreach ($ubigeo as $key => $value) {

            if ($id == $value->id) {
                $selected = true;
            } else {
                $selected = false;
            }

            $datos = array(
                "value" => $value->id,
                "label" => $value->departamento." - ".$value->provincia." - ".$value->distrito,
                "selected" => $selected
            );

            array_push($data,$datos);
        }

        return response()->json($data);
    }

    public function clientes()
    {
        $clientes = Clientes::all();

        $data = array();

        foreach ($clientes as $key => $value) {
            $datos = array(
                "value" => $value->id,
                "label" => $value->razon_social
            );

            array_push($data,$datos);
        }

        return response()->json($data);
    }

    public function detail($id)
    {
        return view('traslado.detail');
    }

    public function show($id){

        
        return view('traslado.show');
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

    public function eliminar($id){

        DB::beginTransaction();

        try{

        $serviciodetallealmacen=new FuncionesController;
        $data= Traslado::find($id);

        if($data->estado==1){

            $data_traslado =DB::table('traslados as t')
            ->join('detalle_traslado as dt','dt.traslado_id','=','t.id')
            ->where('t.id','=',$id)
            ->get();

            $data->estado=3;
            $data->save();

            //print_r($data );exit();


            $fecha = date('Y-m-d');
            $idsede = session('key')->sede_id;
            $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();

           

            foreach ($data_traslado as $key => $value) {
            
               
                $descontar=$serviciodetallealmacen->aumentar_descontar_stock(1, $data->id_ubicacion_origen,$value->producto_id,$value->cantidad,$tipo->tipo_envio);
            }

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "Se guardo correctamente el traslado"
            );

            DB::commit();

            return response()->json( $json);


           

        }else{

            $json = array(
                "respuesta" => "error",
                "mensaje" => "No podemos Anular el traslado porque ya se detecto movimientos!!"
            );

            return response()->json($json);
        }

    }catch (Exception $e) {

        return  response()->json($e);

    }
       

    }


}
