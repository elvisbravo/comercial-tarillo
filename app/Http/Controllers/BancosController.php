<?php

namespace App\Http\Controllers;

use App\Bancos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BancosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('bancos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listadobancos(){

        $bancos = DB::table('bancos')->get();
        //var_dump($bancos); die;
        return response()->json($bancos);

        
    }
    public function crear(Request $request)
    {
        //
        $this->validate($request,[
            'nombre'=>'required'

        ]);

        $bancos=Bancos::create($request->all());

        return response()->json('OK');

    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editarbanco($id)
    {
        //

        $bancos = Bancos::findOrFail($id);
        return response()->json($bancos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function modificar(Request $request)
    {
        //
        $this->validate($request,[
            'nombre'=>'required'

        ]);

        $bancos = Bancos::find($request->id);
        $bancos->nombre=$request->nombre;
        $bancos->abreviatura=$request->abreviatura;
        $bancos->save();

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
        Bancos::find($id)->delete();
        return response()->json('OK');
    }
}
