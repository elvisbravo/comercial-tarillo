<?php

namespace App\Http\Controllers;

use App\Marcas;
use Illuminate\Http\Request;

class MarcasController extends Controller
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
        return view('marcas.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listadomarca(){

        $marcas=Marcas::all();
        
        return response()->json($marcas);

        
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

        $marca=Marcas::create($request->all());

        return response()->json('OK');

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Marcas  $marcas
     * @return \Illuminate\Http\Response
     */
    public function editarmarca($id)
    {
        //

        $marca = Marcas::find($id);

        return response()->json($marca);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Marcas  $marcas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $this->validate($request,[
            'descripcion'=>'required'

        ]);

        $marca = Marcas::find($request->id);
        $marca->descripcion=$request->descripcion;
        $marca->save();

        return response()->json('OK');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Marcas  $marcas
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        Marcas::find($id)->delete();
        return response()->json('OK');
    }
}
