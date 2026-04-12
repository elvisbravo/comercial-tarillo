<?php

namespace App\Http\Controllers;

use App\Vehiculo;
use App\TipoVehiculo;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('vehiculos.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function listadovehiculo(){

        $vehiculo = DB::table('vehiculo')
            ->join('tipo_vehiculo', 'vehiculo.tipo_vehiculo_id', '=', 'tipo_vehiculo.id')
            ->select('vehiculo.*', 'tipo_vehiculo.name')
            ->get();
        

        return response()->json($vehiculo);


        
    }
    public function listadotipovehiculo(){

        $tipo_vehiculo = DB::table('tipo_vehiculo')->get();

        return response()->json($tipo_vehiculo);

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
            'tipo_vehiculo_id'=>'required'

        ]);

        $vehiculo=Vehiculo::create($request->all());

        return response()->json('OK');

    }
    public function editarvehiculo($id)
    {
        //

        $vehiculo = Vehiculo::findOrFail($id);
        return response()->json($vehiculo);
    }
    public function modificar(Request $request)
    {
        //
        $this->validate($request,[
            'tipo_vehiculo_id'=>'required'

        ]);

        $vehiculo = Vehiculo::find($request->id);
        $vehiculo->placa=$request->placa;
        $vehiculo->tipo_vehiculo_id=$request->tipo_vehiculo_id;
        $vehiculo->num_soat=$request->num_soat;
        $vehiculo->color=$request->color;
        $vehiculo->marca=$request->marca;
        $vehiculo->save();

        return response()->json('OK');
    }
    public function eliminar($id)
    {
        //
        Vehiculo::find($id)->delete();
        return response()->json('OK');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
    }
}
