<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Tipo_documento;
use App\Conductor;

class ConductorController extends Controller
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
        $tipo_documento = Tipo_documento::all();
        return view('conductor.index',compact('tipo_documento'));
    }

    public function listadoconductores(){

        $conductor=Conductor::where('estado','=','1')->get();

        return response()->json($conductor);
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
            'nombre'=>'required'

        ]);


        $conductor =new  Conductor;
        $conductor->nombre=$request->nombre;
        $conductor->estado=$request->estado;
        $conductor->numero_documento=$request->numero_documento;
        $conductor->categoria_licencia=$request->categoria_licencia;
        $conductor->num_licencia=$request->num_licencia;
        $conductor->estado=1;
        $conductor->save();

        return response()->json('OK');


    }

   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        //
        $conductor = Conductor::find($id);

        return response()->json($conductor);
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
        $conductor = Conductor::find($request->id);
        $conductor->nombre=$request->nombre;
        $conductor->estado=1;
        $conductor->numero_documento=$request->numero_documento;
        $conductor->categoria_licencia=$request->categoria_licencia;
        $conductor->num_licencia=$request->num_licencia;
        $conductor->estado=1;
        $conductor->save();

        return response()->json($request->all());
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
        $conductor=Conductor::find($id);
        $conductor->estado='0';
        $conductor->save();
    }
}
