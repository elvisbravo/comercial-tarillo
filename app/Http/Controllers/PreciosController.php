<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Precios;
use DB;
use App\Sede;
use App\Productos;

class PreciosController extends Controller
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
        return view('precios.index');
    }


    public function lista_precios(){

        $precios=DB::table('precios as p')
        ->join('lista_precios as l','p.lista_id','=','l.id')
        ->join('productos as pr','p.articulo_id','=','pr.id')
        ->select('p.id','p.id as codigo','pr.nomb_pro','l.descripcion','p.precio_contado','p.precio_credito','p.estado')
        ->where('p.estado','=',1)
        ->get();

        return response()->json($precios);


    }

    //METODO PARA LLEVAR LAS SEDES
    public function sedes(){

        $sedes = Sede::where('estado','=',1)
        //->where('id','!=','1')
        ->get();
        return response()->json($sedes);

    }
    //METODO PARA VALIDAR SI YA SE ASIGNO UN PRODUCTO A UNA SE DE Y LISTA DE PRECISO
    public function validar_producto($id_lista,$id_producto,$id_sede){



        $precios=DB::table('precios as p')
        ->select('p.lista_id')
        ->where('p.lista_id','=',$id_lista)
        ->where('p.articulo_id','=',$id_producto)
        ->where('sucursal_id','=',$id_sede)
        ->get();

        $valor=count($precios);

        return response()->json($valor);

    }


    public function create()
    {
        return view('precios.create');
    }




    public function crear(Request $request)
    {
        //
        /*$this->validate($request,[
            'lista_id'=>'required',
            'articulo_id'=>'required',
            'sucursal_id'=>'required'

        ]);*/

        $precios=new Precios;
        $precios->lista_id=$request->lista_id;
        $precios->articulo_id=$request->articulo_id;
        $precios->sucursal_id=$request->sucursal_id;
        $precios->precio_contado=$request->precio_contado;
        $precios->precio_credito=$request->precio_credito;
        $precios->descuento_contado=$request->descuento_contado;
        //$precios->descuento_credito=$request->descuento_credito;
        $precios->estado='1';
        //$precios->prec_compra=$request->prec_compra;
        $precios->save();

        return response()->json('OK');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $precios=DB::table('precios as p')
        ->join('lista_precios as l','p.lista_id','=','l.id')
        ->join('productos as pr','p.articulo_id','=','pr.id')
        ->select('p.id','l.id as codigo','l.descripcion','p.precio_contado','p.precio_credito','p.estado')
        ->where('p.id','=',$id)
        ->first();

        return response()->json($precios);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $precios = Precios::find($id);
        $productos=Productos::where('id','=', $precios->articulo_id)
        ->select('nomb_pro')
        ->first();
       // print_r($productos->nomb_pro);exit();
         return view('precios.edit',compact('precios','productos'));

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
        $this->validate($request,[
            'lista_id'=>'required',
            'articulo_id'=>'required',
            'sucursal_id'=>'required'

        ]);

        $precios = Precios::find($request->id);
        $precios->lista_id=$request->lista_id;
        $precios->articulo_id=$request->articulo_id;
        $precios->sucursal_id=$request->sucursal_id;
        $precios->precio_contado=$request->precio_contado;
        $precios->precio_credito=$request->precio_credito;
        $precios->descuento_contado=$request->descuento_contado;
        //$precios->descuento_credito=$request->descuento_credito;
        $precios->estado='1';
        //$precios->prec_compra=$request->prec_compra;
        $precios->save();

        return response()->json('OK');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        $precios=Precios::find($id);
        $precios->estado=0;
        $precios->save();

        return response()->json('OK');
    }
}
