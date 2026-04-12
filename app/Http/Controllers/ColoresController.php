<?php

namespace App\Http\Controllers;

use App\Colores;
use Illuminate\Http\Request;


class ColoresController extends Controller
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
        return view('colores.index');
    }

    public function listadocolores(){

        $colores=Colores::all();

        return response()->json($colores);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear(Request $request)
    {
        //

        $this->validate($request,[
            'descripcion'=>'required'

        ]);

        $colores=Colores::create($request->all());

        return response()->json('OK');


    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Colores  $colores
     * @return \Illuminate\Http\Response
     */
    public function editarcolor($id)
    {
        //

        $color = Colores::find($id);

        return response()->json($color);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Colores  $colores
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $this->validate($request,[
            'descripcion'=>'required'

        ]);

        $color = Colores::find($request->id);
        $color->descripcion=$request->descripcion;
        $color->save();

        return response()->json('OK');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Colores  $colores
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        Colores::find($id)->delete();
        return response()->json('OK');
    }
}
