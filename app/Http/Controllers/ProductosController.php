<?php

namespace App\Http\Controllers;

use App\Productos;
use Illuminate\Http\Request;
use App\Unidad_medidas;
use DB;
use App\Almacen;
use App\Detalle_almacen_productos;
use App\Sede;
use App\User;
use Illuminate\Support\Facades\Auth;

class ProductosController extends Controller
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
        $idsede = session('key')->sede_id;
        $origen='';

        if($idsede==1){

            $origen = Almacen::where('sede_id','<>',$idsede)->where('estado','=',1)->get();

        }else{

            $origen = Almacen::where('sede_id','=',$idsede)->where('estado','=',1)->get();
        }

        //print_r($origen);exit();



        return view('productos.index',compact('origen'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){

        $idsede = session('key')->sede_id;
        $origen = Almacen::where('sede_id','=',$idsede)->where('estado','=',1)->get();

        $sedes=Sede::where('estado','=',1)
        ->where('id','<>',1)
        ->select('id','nombre')->get();
        $unidadmedida=Unidad_medidas::all();


         // print_r($sedes);exit();

        return view('productos.create',compact('origen','sedes','unidadmedida'));



    }
    //METODO TRAER LOS TIPOS DE IMPUESTO PARA LAS VENTAS
    public function impuesto_cliente(){

        
        $impuesto = DB::table('impuesto')
            ->join('empresas', 'impuesto.empresa_id', '=', 'empresas.id')
            ->select('impuesto.*', 'empresas.nombre_comercial')
            ->where('tipo_impuesto','=','Ventas')
            ->get();
        

        return response()->json($impuesto);
    }
    //METODO PARA BUSCAR PRODUCTOS
    public function searchproduct($name){
       
        $respuesta=DB::table('productos as p')
        ->join('detalle_almacen_productos as dp','dp.producto_id','=','p.id')
        ->join('stock_location as sl','dp.ubicacion_id','=','sl.id')
        ->join('almacenes as a','sl.almacen_id','=','a.id')
        ->leftjoin('precios as pr','pr.articulo_id','=','p.id')
        ->leftjoin('categorias as c','p.categoria_id','=','c.id')
        ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
        ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
        ->leftjoin('marcas as m','p.marca_id','=','m.id')
        ->leftjoin('colores as co','p.color_id','=','co.id')
        ->select('p.id','p.nomb_pro','p.costo as prec_compra','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
        'm.descripcion as marca','m.id as idmarca','co.descripcion as color','co.id as idcolores',DB::raw('SUM(dp.stock) as stock'),
        'pr.precio_contado','pr.precio_credito')
       ->groupBy('p.id','p.nomb_pro','p.costo','c.categoria','c.id','sub.subcategoria','sub.id','u.descripcion','u.id',
       'm.descripcion','m.id','co.descripcion','co.id','pr.precio_contado','pr.precio_credito')
            ->where('p.estado','=','1')
            ->where('p.nomb_pro','LIKE','%'.$name.'%')
            ->take(20)
            ->get();

            return response()->json($respuesta);

        //print_r($respuesta);exit();

    }
    //METODO PARA CARGAR LOS 10 PRIMERO PRODUCTOS
    public function listaproduct(){
       
        $respuesta=DB::table('productos as p')
        ->leftjoin('detalle_almacen_productos as dp','dp.producto_id','=','p.id')
        ->leftjoin('stock_location as sl','dp.ubicacion_id','=','sl.id')
        ->leftjoin('almacenes as a','sl.almacen_id','=','a.id')
        ->leftjoin('precios as pr','pr.articulo_id','=','p.id')
        ->leftjoin('categorias as c','p.categoria_id','=','c.id')
        ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
        ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
        ->leftjoin('marcas as m','p.marca_id','=','m.id')
        ->leftjoin('colores as co','p.color_id','=','co.id')
        ->select('p.id','p.nomb_pro','p.costo as prec_compra','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
        'm.descripcion as marca','m.id as idmarca','co.descripcion as color','co.id as idcolores',DB::raw('SUM(dp.stock) as stock'),
        'pr.precio_contado','pr.precio_credito')
       ->groupBy('p.id','p.nomb_pro','p.costo','c.categoria','c.id','sub.subcategoria','sub.id','u.descripcion','u.id',
       'm.descripcion','m.id','co.descripcion','co.id','pr.precio_contado','pr.precio_credito')
            ->where('p.estado','=','1')
            //->where('p.nomb_pro','LIKE','%'.$name.'%')
            ->take(12)
            ->get();

            return response()->json($respuesta);

        //print_r($respuesta);exit();

    }

    //METODO PARA LISTAR LOS PRODUCTOS POR SEDE
    public function listarproductos(){

        $idsede = session('key')->sede_id;
        $respuesta="";
        $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();

        $ubicacion=DB::table('stock_location as s')
        ->join('almacenes as a','a.id','=','s.almacen_id')
        ->join('sedes as sd','sd.id','=','a.sede_id')
        ->select('s.id')
        ->where('sd.id','=',$idsede)
        ->where('sd.tipo_envio','=',$tipo->tipo_envio)
        ->where('s.name','=','Stock')
        ->first();

        //print_r($ubicacion->id);exit();

       /* if($id_almacen==0){

            if($idsede==1){

                $respuesta=DB::table('productos as p')
                ->leftjoin('detalle_almacen_productos as dp','dp.producto_id','=','p.id')
                ->leftjoin('stock_location as sl','dp.ubicacion_id','=','sl.id')
                ->leftjoin('almacenes as a','sl.almacen_id','=','a.id')
                ->leftjoin('precios as pr','pr.articulo_id','=','p.id')
                ->leftjoin('categorias as c','p.categoria_id','=','c.id')
                ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
                ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
                ->leftjoin('marcas as m','p.marca_id','=','m.id')
                ->leftjoin('colores as co','p.color_id','=','co.id')
                ->select('p.id','p.nomb_pro','p.costo as prec_compra','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
                 'm.descripcion as marca','m.id as idmarca','p.descuento_mini_venta_cre','p.precio_venta_contado','p.precio_venta_credito','co.descripcion as color','co.id as idcolores',DB::raw('SUM(dp.stock) as stock'),
                  'pr.precio_contado','pr.precio_credito')
                ->groupBy('p.id','p.nomb_pro','p.costo','c.categoria','c.id','sub.subcategoria','sub.id','u.descripcion','u.id',
                'm.descripcion','m.id','p.descuento_mini_venta_cre','p.precio_venta_contado','p.precio_venta_credito','co.descripcion','co.id', 'pr.precio_contado','pr.precio_credito')
                ->where('p.estado','=','1')
                ->get();
                //return response()->json( "HOLA");


            }else{

                $respuesta=DB::table('productos as p')
                ->leftjoin('detalle_almacen_productos as dp','dp.producto_id','=','p.id')
                ->leftjoin('stock_location as sl','dp.ubicacion_id','=','sl.id')
                ->leftjoin('almacenes as a','sl.almacen_id','=','a.id')
                ->leftjoin('precios as pr','pr.articulo_id','=','p.id')
                ->leftjoin('categorias as c','p.categoria_id','=','c.id')
                ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
                ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
                ->leftjoin('marcas as m','p.marca_id','=','m.id')
                ->leftjoin('colores as co','p.color_id','=','co.id')
                ->select('p.id','p.nomb_pro','p.costo as prec_compra','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
                 'm.descripcion as marca','m.id as idmarca','co.descripcion as color','co.id as idcolores',DB::raw('SUM(dp.stock) as stock'),
                 'pr.precio_contado','pr.precio_credito')
                ->groupBy('p.id','p.nomb_pro','p.costo','c.categoria','c.id','sub.subcategoria','sub.id','u.descripcion','u.id',
                'm.descripcion','m.id','co.descripcion','co.id','pr.precio_contado','pr.precio_credito')
                ->where('a.sede_id','=', $idsede)
                ->where('p.estado','=','1')
                ->get();
              


            }


        }else{*/

           
            $respuesta=DB::table('productos as p')
            ->leftjoin('detalle_almacen_productos as dp','dp.producto_id','=','p.id')
            ->leftjoin('stock_location as sl','dp.ubicacion_id','=','sl.id')
            ->leftjoin('almacenes as a','sl.almacen_id','=','a.id')
            ->leftjoin('precios as pr','pr.articulo_id','=','p.id')
            ->leftjoin('categorias as c','p.categoria_id','=','c.id')
            ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
            ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
            ->leftjoin('marcas as m','p.marca_id','=','m.id')
            ->leftjoin('colores as co','p.color_id','=','co.id')
            ->select('p.id','p.nomb_pro','p.costo as prec_compra','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
             'm.descripcion as marca','m.id as idmarca','co.descripcion as color','co.id as idcolores',DB::raw('SUM(dp.stock) as stock'),
             'pr.precio_contado','pr.precio_credito')
            ->groupBy('p.id','p.nomb_pro','p.costo','c.categoria','c.id','sub.subcategoria','sub.id','u.descripcion','u.id',
            'm.descripcion','m.id','co.descripcion','co.id','pr.precio_contado','pr.precio_credito')
            ->where('a.sede_id','=', $idsede)
            ->where('dp.tipo_envio','=', $tipo->tipo_envio)
            ->where('dp.ubicacion_id','=',$ubicacion->id)
            ->where('p.estado','=','1')
            ->get();



        //}

       return response()->json( $respuesta);





    }
    //metodo para listar la subcategorias
    public function subcategorias($id){

        $subcategorias=DB::table('sub_categorias as s')
        ->select('s.id','s.subcategoria')
        ->where('s.estado','=',1)
        ->where('s.categoria_id','=',$id)
        ->get();

        return response()->json($subcategorias);

    }
    //metodo para cargar las unidades de medida
    public function unidades(){

        $unidadmedida=Unidad_medidas::all();

        return response()->json($unidadmedida);

    }
    public function listadoproductos()
    {
        //
        //$usuario = $request->session()->get('key');

        $productos=DB::table('productos as p')
        ->leftjoin('categorias as c','p.categoria_id','=','c.id')
        ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
        ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
        ->leftjoin('marcas as m','p.marca_id','=','m.id')
        ->select('p.id','p.nomb_pro','p.prec_ven','p.precio_venta_credito','c.categoria','sub.subcategoria','u.descripcion as unidad','m.descripcion as marca')
        ->where('p.estado','=','1')
        ->get();


        return response()->json($productos);

    }

    //funcion consultar si ya se creo el producto en el almacen
    public function consultacreacionalmacen($id_producto,$id_almacen){

        $producto=DB::table('detalle_almacen_productos as dt')
        ->select('dt.id')
        ->where('dt.almacen_id','=',$id_almacen)
        ->where('dt.producto_id','=',$id_producto)
        ->get();

        return response()->json($producto);
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
        $this->validate($request, [
            'nomb_pro' => 'required',
            'codigo_barras' => 'unique:productos'
             ]);

           $name = session('key')->name;

           $idsede = session('key')->sede_id;
           $tipo=DB::table('sedes as s')->select('s.tipo_envio')->where('s.id','=',$idsede )->first();

           //print_r($request->all());exit();
         //METODO PARA GUARDAR LOS PRODUCTOS
            $productos=new Productos;
            $productos->nomb_pro=$request->nomb_pro;
            //$productos->prec_compra=$request->prec_compra;
            $productos->impuesto_id=$request->impuesto_id;
            $productos->volumen=$request->volumen;
            $productos->peso=$request->peso;
            $productos->codigo_barras=$request->codigo_barras;

            if($request->hasFile("img")):

                $file = $request->file("img");
                $nombre = $file->getClientOriginalName();
                $ruta= public_path("img/productos");
                $productos->img=$nombre;
                $file->move($ruta,$nombre);

            endif;

            $productos->modelo_id=$request->modelo_id;
            $productos->unidad_medida_id=$request->unidad_medida_id;
            $productos->marca_id=$request->marca_id;
            $productos->color_id=$request->color_id;
            $productos->categoria_id=$request->categoria_id;
            $productos->subcategoria_id=$request->subcategoria_id;
            $productos->estado='1';
            $productos->stock_minimo=$request->stock_minimo;
            $productos->usuario_registro=$name;
            $productos->costo=$request->costo;
            $productos->save();
         //METODO PARA GUARDAR LOS PRODUCTOS EN EL DETALLE ALMACEN
         //$sedes=$request->sedes;
         $sedes=Sede::where('estado','=',1)
         //->where('id','<>',1)
         ->get();

        for($i=0; $i <count($sedes);$i++){

            //Almacen::where('sede_id','=',$sedes[$i]->id)
            $origen =DB::table('almacenes as a')
            ->join('stock_location as sl','sl.almacen_id','=','a.id')
            //->where('estado','=',1)
            //->where('sl.name','Stock')
            ->select('sl.id')
            ->get();

            foreach($origen as $or){

                
                $detalle=Detalle_almacen_productos::where('producto_id','=',$productos->id)
                ->where('tipo_envio','=',0)->where('ubicacion_id','=',$or->id)->first();

                if($detalle==null){
                    $detalle=new Detalle_almacen_productos;
                }
                

                $detalle->stock=0.00;
                $detalle->tipo_envio=0;
                $detalle->ubicacion_id=$or->id;
                $detalle->producto_id=$productos->id;
                $detalle->estado='1';
                $detalle->save();
                
            }
            foreach($origen as $or){

                $detalle=Detalle_almacen_productos::where('producto_id','=',$productos->id)
                ->where('tipo_envio','=',1)->where('ubicacion_id','=',$or->id)->first();

                if($detalle==null){

                    $detalle=new Detalle_almacen_productos;
                }

                $detalle->stock=0.00;
                $detalle->tipo_envio=1;
                $detalle->ubicacion_id=$or->id;
                $detalle->producto_id=$productos->id;
                $detalle->estado='1';
                $detalle->save();
            }




        }

         //$origen = Almacen::where('sede_id','=',$idsede)->where('estado','=',1)->get();

        // print_r("hola");exit();

        //NO SE ENVIA EL ID MODELO Y EL PRECIO_VEN

       //print_r($request->categoria_id);exit();
      /* if($request->hasFile("img")):

        $product=Productos::find($request->producto_id);

        $file = $request->file("img");
        $nombre = $file->getClientOriginalName();
        $ruta= public_path("img/productos");
        $product->img=$nombre;
        $file->move($ruta,$nombre);
        $product->save();

    endif;


        $productos=new Detalle_almacen_productos;
        $productos->stock=0.00;
        $productos->tipo_envio=0;
        $productos->almacen_id=$request->almacen_id;
        $productos->producto_id=$request->producto_id;
        $productos->prec_ven=$request->prec_ven;
        $productos->controlstock=$request->controlstock;
        $productos->porcentaje_ganancia=$request->porcentaje_ganancia;
        $productos->porcentaje_venta_credito=$request->porcentaje_venta_credito;
        $productos->precio_venta_credito=$request->precio_venta_credito;
        $productos->stock_minimo=$request->stock_minimo;
        $productos->save();*/


        /*$productos->nomb_pro=$request->nomb_pro;
        $productos->prec_compra=$request->prec_compra;
        $productos->prec_ven=$request->prec_ven;
        $productos->cuenta_debe=$request->cuenta_debe;
        $productos->cuenta_haber=$request->cuenta_haber;
        $productos->controlstock=$request->controlstock;
        $productos->codigo_barras=$request->codigo_barras;

        if($request->hasFile("img")):

            $file = $request->file("img");
            $nombre = $file->getClientOriginalName();
            $ruta= public_path("img/productos");
            $productos->img=$nombre;
            $file->move($ruta,$nombre);

        endif;
        $productos->modelo_id=$request->modelo_id;
        $productos->unidad_medida_id=$request->unidad_medida_id;
        $productos->marca_id=$request->marca_id;
        $productos->color_id=$request->color_id;
        $productos->porcentaje_ganancia=$request->porcentaje_ganancia;
        $productos->categoria_id=$request->categoria_id;
        $productos->subcategoria_id=$request->subcategoria_id;
        $productos->stock_minimo=$request->stock_minimo;
        $productos->porcentaje_venta_credito=$request->porcentaje_venta_credito;
        $productos->precio_venta_credito=$request->precio_venta_credito;
        $productos->estado='Activo';
        $productos->save();*/


        return redirect()->route('productos.index')
        ->with('success','Producto created successfully');



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Productos  $productos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $unidadmedida=Unidad_medidas::all();

        $productos=Productos::find($id);

        return view('productos.show',compact('unidadmedida','productos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Productos  $productos
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $unidadmedida=Unidad_medidas::all();
        $productos=Productos::find($id);

        //
        /*$productos=DB::table('detalle_almacen_productos as dp')
            ->join('almacenes as a','dp.almacen_id','=','a.id')
            ->join('productos as p','dp.producto_id','=','p.id')
            ->leftjoin('categorias as c','p.categoria_id','=','c.id')
            ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
            ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
            ->leftjoin('marcas as m','p.marca_id','=','m.id')
            ->leftjoin('colores as co','p.color_id','=','co.id')
            ->select('dp.id','p.id as idproducto','a.id as almacen_id','p.codigo','p.nomb_pro','p.prec_compra','c.categoria','c.id as categoria_id','sub.subcategoria','sub.id as subcategoria_id','u.descripcion as unidad','u.id as unidad_medida_id',
            'm.descripcion as marca','m.id as marca_id','p.descuento_minimo_venta_cont','co.descripcion as color','co.id as color_id','a.nombre as almacen',
            'dp.stock','dp.prec_ven','dp.precio_venta_credito','dp.porcentaje_ganancia','dp.porcentaje_venta_credito','p.cuenta_debe','p.cuenta_haber','p.codigo_barras','p.img')
            ->where('p.id','=', $id)
            ->first();*/


        //print_r($productos);exit();

        return view('productos.edit',compact('unidadmedida','productos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Productos  $productos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $this->validate($request, [
            'nomb_pro' => 'required',
            //'prec_compra' => 'required'
        ]);

        $name = session('key')->name;

        //NO SE ENVIA EL ID MODELO Y EL PRECIO_VEN
        $productos=Productos::find($request->id);
        $productos->nomb_pro=$request->nomb_pro;
        //$productos->prec_compra=$request->prec_compra;
        $productos->impuesto_id=$request->impuesto_id;
        $productos->volumen=$request->volumen;
        $productos->peso=$request->peso;
        $productos->codigo_barras=$request->codigo_barras;

        if($request->hasFile("img")):

            $file = $request->file("img");
            $nombre = $file->getClientOriginalName();
            $ruta= public_path("img/productos");
            $productos->img=$nombre;
            $file->move($ruta,$nombre);

        endif;

        $productos->modelo_id=$request->modelo_id;
        $productos->unidad_medida_id=$request->unidad_medida_id;
        $productos->marca_id=$request->marca_id;
        $productos->color_id=$request->color_id;
        $productos->categoria_id=$request->categoria_id;
        $productos->subcategoria_id=$request->subcategoria_id;
        $productos->estado='1';
        $productos->stock_minimo=$request->stock_minimo;
        $productos->usuario_registro=$name;
        $productos->costo=$request->costo;
        $productos->save();






        return redirect()->route('productos.index')
        ->with('success','Producto Modificado successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Productos  $productos
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        $detalle=Detalle_almacen_productos::where('producto_id','=',$id)
        ->select(DB::raw('SUM(stock) as stock'))
        ->get();

        $respuesta='';

        if($detalle[0]->stock==0){

            $producto=Productos::find($id);
            $producto->estado='2';
            $producto->save();
            $detalle=Detalle_almacen_productos::where('producto_id','=',$id)
            ->get();


            foreach($detalle as $d){

                 $d->estado='2';
                 $d->save();
            }

        }else{

            $respuesta='ERROR';

        }

        return response()->json($respuesta);






    }
}
