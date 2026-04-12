<?php

namespace App\Http\Controllers;

use App\Modelos;
use Illuminate\Http\Request;

class ModelosController extends Controller
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
        return view('modelos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function litadomodelos(){

        $modelos=Modelos::all();

        return response()->json($modelos);
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
        $this->validate($request,[
            'descripcion'=>'required'

        ]);

        $modelos=Modelos::create($request->all());

        return response()->json('OK');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modelos  $modelos
     * @return \Illuminate\Http\Response
     */
    public function editarmodelo($id)
    {
        //

        $modelos = Modelos::find($id);

        return response()->json($modelos);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Modelos  $modelos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $this->validate($request,[
            'descripcion'=>'required'

        ]);

        $modelo = Modelos::find($request->id);
        $modelo->descripcion=$request->descripcion;
        $modelo->save();

        return response()->json('OK');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Modelos  $modelos
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        Modelos::find($id)->delete();
        return response()->json('OK');
    }
}
