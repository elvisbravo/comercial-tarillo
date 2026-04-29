<?php

namespace App\Http\Controllers;

use App\Kardex;
use App\Almacen;
use App\Productos;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Controllers\servicios\FuncionesController;

class KardexController extends Controller
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
        $idsede = session('key')->sede_id;
        $origen = Almacen::where('sede_id','=',$idsede)->where('estado','=',1)->get();
        return view('kardex.index',compact('origen'));
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

    public function traer_productos(Request $request)
    {
        $data = [];

        $tipo_envio = new FuncionesController;

        $envio = $tipo_envio->tipo_envio_sunat();

        if (!isset($request->q)) {
            $productos = Productos::skip(0)->take(10)->get();
        } else {
            $productos = Productos::where('nomb_pro','like','%'.$request->q.'%')->get();
        }

        foreach ($productos as $key => $value) {
            $data["results"][$key]["id"] = $value->id;
			$data["results"][$key]["text"] = $value->nomb_pro;
        }

        return response()->json($data);
    }

    public function traer_ubicaciones($id)
    {
        $ubicaciones = DB::table('stock_location')
            ->where('almacen_id', $id)
            ->where('estado', '1')
            ->get();
        return response()->json($ubicaciones);
    }

    public function guardar(Request $request)
    {
        $this->validate($request, [
            'producto' => 'required',
            'almacen' => 'required',
            'fecha_inicio' => 'required',
            'fecha_final' => 'required',
        ]);

        $tipo_envio = new FuncionesController;

        $envio = $tipo_envio->tipo_envio_sunat();

        $query = DB::table('kardexes')
            ->join('tipo_comprobantes', 'kardexes.tipo_comprobante', '=', 'tipo_comprobantes.id')
            ->join('stock_location', 'kardexes.ubicacion_id', '=', 'stock_location.id')
            ->select('kardexes.*', 'tipo_comprobantes.descripcion as comprobante', 'stock_location.name as nombre_ubicacion')
            ->where('kardexes.producto_id',$request->producto)
            ->where('kardexes.tipo_envio',$envio)
            ->whereBetween('kardexes.fecha', [$request->fecha_inicio, $request->fecha_final]);

        if ($request->has('ubicacion') && $request->ubicacion != 'todas') {
            $query->where('kardexes.ubicacion_id', $request->ubicacion);
        } else {
            $query->where('stock_location.almacen_id', $request->almacen);
        }

        $kardex = $query->orderBy('kardexes.fecha', 'asc')->get();

        return response()->json($kardex);
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
     * @param  \App\Kardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function show(Kardex $kardex)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function edit(Kardex $kardex)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kardex $kardex)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kardex  $kardex
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kardex $kardex)
    {
        //
    }
}
