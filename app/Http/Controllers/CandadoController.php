<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\candado;

class CandadoController extends Controller
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
        return view('candados-creditos.index');

    }

    public function listacandados(){

        $candados=candado::all();
        return response()->json($candados);

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


        $condados=new candado;
        $condados->rango_minimo=$request->rango_minimo;
        $condados->rango_maximo=$request->rango_maximo;
        $condados->monto_inicial=$request->monto_inicial;
        $condados->nmeses=$request->nmeses;
        $condados->user_name=$request->user_name;
        $condados->estado=1;
        $condados->save();

        return response()->json('OK');

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $candado = candado::find($id);
        return response()->json($candado);

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


        $color = candado::find($request->id);
        $color->rango_minimo=$request->rango_minimo;
        $color->rango_maximo=$request->rango_maximo;
        $color->monto_inicial=$request->monto_inicial;
        $color->nmeses=$request->nmeses;
        $color->user_name=$request->user_name;
        $color->estado=1;
        $color->save();

        return response()->json('OK');

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
        candado::find($id)->delete();
        return response()->json('OK');
    }
}
