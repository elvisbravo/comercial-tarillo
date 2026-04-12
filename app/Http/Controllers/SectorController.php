<?php

namespace App\Http\Controllers;

use App\Sector;
use Illuminate\Http\Request;

class SectorController extends Controller
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
        return view('sectores.index');
    }
    public function listado(){

         $sectores=Sector::all();
         return response()->json($sectores);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
        $this->validate($request,[
            //'descripcion'=>'required'

        ]);

        $sector =new Sector;
        $sector->nomb_sec=$request->nomb_sec;
        $sector->estado='ACTIVO';
        $sector->save();

        return response()->json('OK');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sector  $sector
     * @return \Illuminate\Http\Response
     */
    public function show(Sector $sector)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sector  $sector
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $sector = Sector::find($id);

        return response()->json($sector);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sector  $sector
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
       /* $this->validate($request,[
            'descripcion'=>'required'

        ]);*/

        $sector = Sector::find($request->id);
        $sector->nomb_sec=$request->nomb_sec;
        $sector->estado='ACTIVO';
        $sector->save();

        return response()->json('OK');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sector  $sector
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        //
        $sector = Sector::find($id);
        $sector->estado='INACTIVO';
        $sector->save();
        return response()->json('OK');
    }
}
