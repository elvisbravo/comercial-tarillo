<?php

namespace App\Http\Controllers;

use App\SubCategorias;
use Illuminate\Http\Request;
use App\Categorias;
use DB;

class SubCategoriasController extends Controller
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
        return view('subcategorias.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado(){

        $subcategorias=DB::table('sub_categorias as s')
        ->join('categorias as c','s.categoria_id','=','c.id')
        ->select('s.id','c.categoria as categoria','s.subcategoria','s.estado')
        ->get();

        return response()->json($subcategorias);


    }

    public function listadocategorias(){

        $categorias=Categorias::where('estado','=',1)->get();

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

        $subCategorias=new SubCategorias;
        $subCategorias->subcategoria=$request->subcategoria;
        $subCategorias->estado=1;
        $subCategorias->categoria_id=$request->categoria_id;
        $subCategorias->save();

        return response()->json('OK');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubCategorias  $subCategorias
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        //
        $subCategorias = SubCategorias::findOrFail($id);
        return response()->json($subCategorias);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubCategorias  $subCategorias
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //

        $subCategorias = SubCategorias::findOrFail($request->id);
        $subCategorias->subcategoria=$request->subcategoria;
        $subCategorias->estado=1;
        $subCategorias->categoria_id=$request->categoria_id;
        $subCategorias->save();
        return response()->json('OK');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubCategorias  $subCategorias
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        $subCategorias = SubCategorias::findOrFail($id);
        $subCategorias->estado=0;
        $subCategorias->save();
        return response()->json('OK');

    }

    public function activar($id){

        $subCategorias = SubCategorias::findOrFail($id);
        $subCategorias->estado=1;
        $subCategorias->save();
        return response()->json('OK');


    }
}
