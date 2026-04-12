<?php

namespace App\Http\Controllers;

use App\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
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
        return view('almacenes.index');
    }

    public function render()
    {
        $idsede = session('key')->sede_id;

        $almacenes = Almacen::where('sede_id','=',$idsede)->where('sede_id','!=',1)->where('estado','=',1)->get();

        return response()->json($almacenes);
    }

    public function guardar(Request $request)
    {
        $this->validate($request, [
            'nombre_almacen' => 'required',
            'direccion_almacen' => 'required',
        ]);

        $nombre = $request->nombre_almacen;
        $direccion = $request->direccion_almacen;
        $idalmacen = $request->idalmacen;
        $abreviatura=$request->abreviatura;

        $idsede = session('key')->sede_id;

        if ($idalmacen == 0) {
            $almacen = new Almacen;

            $almacen->sede_id = $idsede;
            $almacen->nombre = $nombre;
            $almacen->direccion = $direccion;
            $almacen->abreviatura=$abreviatura;
            $almacen->estado = 1;
            $almacen->save();

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "Se agrego correctamente el almacen"
            );

        } else {
            $almacen = Almacen::find($idalmacen);
            $almacen->nombre = $nombre;
            $almacen->direccion = $direccion;
            $almacen->abreviatura=$abreviatura;
            $almacen->save();

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "Se edito correctamente el almacen"
            );
        }

        return response()->json($json);

    }

    public function eliminar($id)
    {
        $almacen = Almacen::find($id);

        $almacen->estado = 0;

        $almacen->save();

        return response()->json("ok");
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Almacen  $almacen
     * @return \Illuminate\Http\Response
     */
    public function show(Almacen $almacen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Almacen  $almacen
     * @return \Illuminate\Http\Response
     */
    public function edit(Almacen $almacen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Almacen  $almacen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Almacen $almacen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Almacen  $almacen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Almacen $almacen)
    {
        //
    }
}
