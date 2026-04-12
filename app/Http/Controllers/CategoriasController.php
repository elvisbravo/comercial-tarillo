<?php

namespace App\Http\Controllers;

use App\Categorias;
use Illuminate\Http\Request;

class CategoriasController extends Controller
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
        return view('categorias.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado(){

        $categorias=Categorias::all();

        return response()->json($categorias);


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


        $categorias=new Categorias;
        $categorias->categoria=$request->categoria;
        $categorias->estado=1;
        $categorias->save();

        return response()->json('OK');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categorias  $categorias
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        //
        $categorias=Categorias::find($id);

        return response()->json($categorias);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Categorias  $categorias
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $categorias=Categorias::find($request->id);
        $categorias->categoria=$request->categoria;
        $categorias->estado=1;
        $categorias->save();

        return response()->json('OK');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Categorias  $categorias
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        $categorias=Categorias::find($id);
        $categorias->estado=0;
        $categorias->save();

    }

    public function activar($id){

        $categorias=Categorias::find($id);
        $categorias->estado=1;
        $categorias->save();


    }
}
