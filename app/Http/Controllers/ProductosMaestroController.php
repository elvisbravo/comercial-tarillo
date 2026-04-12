<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unidad_medidas;
use DB;
use App\Productos;
use App\Almacen;
use App\Detalle_almacen_productos;
use App\Sede;
use Illuminate\Support\Facades\Auth;

class ProductosMaestroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        $usuario = $request->session()->get('key');

        if($usuario->sede_id==1){



            return view('productos-maestro.index');

        }else{

            return redirect()->route('home');
        }


    }

    public function listadoproductosmaestro()
    {
        //
        //$usuario = $request->session()->get('key');

        $productos=DB::table('productos as p')
        ->leftjoin('categorias as c','p.categoria_id','=','c.id')
        ->leftjoin('sub_categorias as sub','p.subcategoria_id','=','sub.id')
        ->leftjoin('unidad_medidas as u','p.unidad_medida_id','=','u.id')
        ->leftjoin('marcas as m','p.marca_id','=','m.id')
        ->leftjoin('colores as co','p.color_id','=','co.id')
        ->select('p.id','p.codigo','p.nomb_pro','p.prec_compra','c.categoria','c.id as idcategoria','sub.subcategoria','sub.id as idsub','u.descripcion as unidad','u.id as idunidad',
          'm.descripcion as marca','m.id as idmarca','co.descripcion as color','co.id as idcolores')
        ->get();


        return response()->json($productos);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $unidadmedida=Unidad_medidas::all();

        $sedes=Sede::where('estado','=',1)
        ->where('id','<>',1)
        ->select('id','nombre')->get();

        return view('productos-maestro.create',compact('unidadmedida','sedes'));
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
            'prec_compra' => 'required',
            'codigo' => 'required',
            'descuento_minimo_venta_cont' => 'required',
            'descuento_mini_venta_cre' => 'required',
            'precio_venta_contado'=> 'required',
            'stock_minimo' => 'required',
            'precio_venta_credito'=>'required'
        ]);

        $user = Auth::user();

        //print_r();exit();



        $productos=new Productos;
        $productos->nomb_pro=$request->nomb_pro;
        $productos->prec_compra=$request->prec_compra;
        $productos->cuenta_debe=$request->cuenta_debe;
        $productos->cuenta_haber=$request->cuenta_haber;
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
        $productos->estado='Activo';
        $productos->codigo=$request->codigo;
        $productos->precio_venta_contado=$request->precio_venta_contado;
        $productos->precio_venta_credito=$request->precio_venta_credito;
        $productos->descuento_minimo_venta_cont=$request->descuento_minimo_venta_cont;
        $productos->porcentaje_ganancia_venta=$request->porcentaje_ganancia_venta;
        $productos->porcentaje_venta_credito=$request->porcentaje_venta_credito;
        $productos->descuento_mini_venta_cre=$request->descuento_mini_venta_cre;
        $productos->stock_minimo=$request->stock_minimo;
        $productos->usuario_registro=$user->name;
        $productos->save();

        //print_r($productos);exit();



          $sedes=$request->sedes;

          print_r($sedes);exit();

        for($i=0; $i <count($sedes);$i++){

            $origen = Almacen::where('sede_id','=',$sedes[$i])
            ->where('estado','=',1)
            ->select('id')
            ->get();

            foreach($origen as $or){

                $detalle=new Detalle_almacen_productos;
                $detalle->stock=0.00;
                $detalle->tipo_envio=0;
                $detalle->almacen_id=$or->id;
                $detalle->producto_id=$productos->id;
                $detalle->estado='Activo';
                $detalle->save();


            }



        }





        return redirect()->route('productos-maestro.index')
        ->with('success','Producto created successfully');





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
        $unidadmedida=Unidad_medidas::all();

        $productos=Productos::find($id);

        return view('productos-maestro.show',compact('unidadmedida','productos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unidadmedida=Unidad_medidas::all();
        $sedes=Sede::where('estado','=',1)
        ->where('id','<>',1)
        ->select('id','nombre')->get();

        $productos=Productos::find($id);

        $detalle=DB::table('detalle_almacen_productos as dt')
        ->join('almacenes as a','dt.almacen_id','=','a.id')
        ->join('sedes as s','a.sede_id','=','s.id')
        ->where('dt.producto_id','=',$id)
        ->pluck('s.id','s.id')
        ->all();

        //print_r($productos->stock_minimo);exit();

        return view('productos-maestro.edit',compact('unidadmedida','productos','sedes','detalle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $this->validate($request, [
            'nomb_pro' => 'required',
            'prec_compra' => 'required',
            'codigo' => 'required',
            'descuento_minimo_venta_cont' => 'required',
            'descuento_mini_venta_cre' => 'required',
            'precio_venta_contado'=> 'required',
            'stock_minimo' => 'required',
            'precio_venta_credito'=>'required'
        ]);

        $user = Auth::user();

        $productos=Productos::find($request->id);
        $productos->nomb_pro=$request->nomb_pro;
        $productos->prec_compra=$request->prec_compra;
        $productos->cuenta_debe=$request->cuenta_debe;
        $productos->cuenta_haber=$request->cuenta_haber;
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
        $productos->estado='Activo';
        $productos->codigo=$request->codigo;
        $productos->precio_venta_contado=$request->precio_venta_contado;
        $productos->precio_venta_credito=$request->precio_venta_credito;
        $productos->descuento_minimo_venta_cont=$request->descuento_minimo_venta_cont;
        $productos->porcentaje_ganancia_venta=$request->porcentaje_ganancia_venta;
        $productos->porcentaje_venta_credito=$request->porcentaje_venta_credito;
        $productos->descuento_mini_venta_cre=$request->descuento_mini_venta_cre;
        $productos->stock_minimo=$request->stock_minimo;
        $productos->usuario_modifico=$user->name;
        $productos->save();

        //print_r($productos);exit();




          $sedes=$request->sedes;

         // print_r($sedes);exit();


        for($i=0; $i <count($sedes);$i++){

            $origen = Almacen::where('sede_id','=',$sedes[$i])
            ->where('estado','=',1)
            ->select('id')
            ->get();

            foreach($origen as $or){

                $exite=$this->Validar_existencia_almacen($productos->id,$or->id);




                if($exite==0)
                {

                    $detalle=new Detalle_almacen_productos;
                    $detalle->stock=0.00;
                    $detalle->tipo_envio=0;
                    $detalle->almacen_id=$or->id;
                    $detalle->producto_id=$productos->id;
                    $detalle->save();

                }else{

                    //VALIDAR SI EL PRODUCTO TIENE STOCK SI TIENE STOCK NO SE PODRA DESACTIVAR DEL ALAMCEN

                    $stock=$this->validar_Stock_producto($productos->id,$or->id);
                    //print_r($stock);exit();


                     /*if($stock==0){

                            $detalle=DB::table('detalle_almacen_productos')
                            ->where('almacen_id','=',$productos->id)
                            ->where('producto_id','=',$or->id)
                            ->first();

                            $detalle->estado='Activo';
                            $detalle->save();



                     }*/




                }





            }



        }



        return redirect()->route('productos-maestro.index')
        ->with('success','Producto modificado successfully');
    }

    //METODO PARA VALIDAR SI EL PRODCUTO ESTA EN EL ALMACEN

       public function Validar_existencia_almacen($id_producto,$id_almacen)
       {

        $detalle=DB::table('detalle_almacen_productos')
        ->select('almacen_id','producto_id')
        ->where('almacen_id','=',$id_almacen)
        ->where('producto_id','=',$id_producto)
        ->get();

        $total=count($detalle);

        return $total;



       }

      public function validar_Stock_producto($id_producto,$id_almacen){

        $detalle=DB::table('detalle_almacen_productos')
        ->select('stock')
        ->where('almacen_id','=',$id_almacen)
        ->where('producto_id','=',$id_producto)
        ->get();

        $valor=$detalle[0]->stock;

        return $valor;



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
