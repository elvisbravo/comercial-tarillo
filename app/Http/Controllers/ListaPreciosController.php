<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\lista_precios;

class ListaPreciosController extends Controller
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

        return view('lista-precios.index');
    }

    public function listaprecios(){

        $lista=lista_precios::where('estado','=','1')->get();

        return response()->json($lista);
    }

    public function crear(Request $request)
    {
        //
        $this->validate($request,[
            'descripcion'=>'required',
            'vigencia'=>'required'

        ]);



        $marca=new lista_precios;
        $marca->descripcion=$request->descripcion;
        $marca->vigencia=$request->vigencia;
        $marca->fecha_inicial=$request->fecha_inicial;
        $marca->fecha_final=$request->fecha_final;
        $marca->estado='1';
        $marca->save();


        return response()->json('OK');

    }

    public function editarlista($id)
    {
        //

        $marca = lista_precios::find($id);

        return response()->json($marca);
    }






    public function update(Request $request)
    {
        //
        $this->validate($request,[
            'descripcion'=>'required',
            'vigencia'=>'required'

        ]);

        $lista = lista_precios::find($request->id);
        $lista->descripcion=$request->descripcion;
        $lista->vigencia=$request->vigencia;
        $lista->fecha_inicial=$request->fecha_inicial;
        $lista->fecha_final=$request->fecha_final;
        $lista->estado='1';
        $lista->save();

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
        $lista=lista_precios::find($id);
        $lista->estado='0';
        $lista->save();

        return response()->json('OK');
    }
}
