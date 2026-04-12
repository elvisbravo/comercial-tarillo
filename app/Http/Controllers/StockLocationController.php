<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\StokLocation;
use App\TipoHubicaciones;
use App\Almacen;
use DB;

class StockLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ubicaciones=DB::table('stock_location as l')
        ->select('l.id','l.name','l.almacen_id','l.estado')
        ->get();

        $almacenes=Almacen::all();



        //print_r($ubicaciones);exit();
        return view('stock-location.index',compact('ubicaciones','almacenes'));
    }

    //METODO PARA DEVOLVER LOS ALMACENES QUE ESTAN DENTRO DE CADA UBICACION
   

    public function tipohubicacion(){

        $tipo=TipoHubicaciones::all();

        return response()->json($tipo);
    }

    public function almacenes(){

        $tipo=Almacen::where('id','<>',4)->get();

        return response()->json($tipo);


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
        $location=new StokLocation;
        $location->name=$request->name;
        $location->almacen_id=$request->almacen_id;
        $location->responsable=$request->responsable;
        $location->type_location=$request->type_location;
        $location->es_chatarra=$request->es_chatarra;
        $location->devolucion=$request->devolucion;
        $location->estado='1';
        $location->save();

        return response()->json($location);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $ubicacion = StokLocation::find($id);
        return response()->json($ubicacion);


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
        $location=StokLocation::find($request->id);
        $location->name=$request->name;
        $location->almacen_id=$request->almacen_id;
        $location->responsable=$request->responsable;
        $location->type_location=$request->type_location;
        $location->es_chatarra=$request->es_chatarra;
        $location->devolucion=$request->devolucion;
        $location->estado='1';
        $location->save();

        return response()->json($location);
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

        $location = StokLocation::find($id);
        $location->estado='0';
        $location->save();
        return response()->json('OK');
    }

    public function activar($id)
    {
        //

        $location = StokLocation::find($id);
        $location->estado='1';
        $location->save();
        return response()->json('OK');
    }
}
