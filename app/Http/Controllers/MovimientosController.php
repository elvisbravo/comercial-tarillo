<?php

namespace App\Http\Controllers;

use App\Movimientos;
use App\Forma_pago;
use App\Caja;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\servicios\FuncionesController;
use Illuminate\Http\Request;

class MovimientosController extends Controller
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


    public function index(Request $request)
    {
        $servicios = new FuncionesController;

        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $envio = $servicios->tipo_envio_sunat();

        $mov = DB::table('movimientos')
            ->join('conceptos','conceptos.id','=','movimientos.concepto_id')
            ->join('tipo_comprobantes','tipo_comprobantes.id','=','movimientos.tipo_comprobante_id')
            ->select('movimientos.id',DB::raw("to_char(movimientos.fecha, 'DD-MM-YYYY') as fecha"),'movimientos.tipo_caja','movimientos.descripcion as desc_mov','movimientos.monto','conceptos.tipo_movimiento','movimientos.descripcion_comprobante', 'movimientos.hora')
            ->where('movimientos.estado','=',1)
            ->where('movimientos.sede_id','=',$idsede)
            ->where('movimientos.tipo_envio','=',$envio)
            ->orderBy('movimientos.id','desc')
            ->paginate(10);

        return view('movimientos.index',compact('mov'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['forma_pagos'] = Forma_pago::all();
        return view('movimientos.create', $data);
    }

    public function guardar(Request $request)
    {
        $mov = new Movimientos;

        $servicios = new FuncionesController;

        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $envio = $servicios->tipo_envio_sunat();

        $caja = Caja::where('user_id','=',$user_id)->where('tipo_envio','=',$envio)->where('sede_id','=',$idsede)->orderBy('id','desc')->limit(1)->first();
        $id_caja = $caja->id;

        if ($request->forma_pago == 1) {
            $tipo_caja = 'FISICA';
            $total = $servicios->total_caja_fisica($id_caja);
        } else {
            $tipo_caja = 'VIRTUAL';
            $total = $servicios->total_caja_virtual($id_caja);
        }


        if ($request->tipo_movimiento == "EGRESO") {
            if ($request->monto > $total) {
                $json = array(
                    "respuesta" => "error",
                    "mensaje" => "No hay saldo en caja"
                );

                return response()->json($json);
            }
        }

        $mov->id_sesion_caja = $id_caja;
        $mov->forma_pago_id = $request->forma_pago;
        $mov->concepto_id = $request->concepto;
        $mov->fecha = date('Y-m-d');
        $mov->hora = date('H:i:s');
        $mov->monto = $request->monto;
        $mov->descripcion = $request->descripcion;
        $mov->tipo_comprobante_id = 5;
        $mov->moneda_id = 1;
        $mov->descripcion_comprobante = "";
        $mov->tipo_envio = $envio;
        $mov->sede_id = $idsede;
        $mov->tipo_caja = $tipo_caja;
        $mov->estado = 1;

        $mov->save();

        $json = array(
            "respuesta" => "ok",
            "mensaje" => "Se guardo correctamente el ".$request->tipo_movimiento
        );

        return response()->json($json);
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
     * @param  \App\Movimientos  $movimientos
     * @return \Illuminate\Http\Response
     */
    public function show(Movimientos $movimientos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Movimientos  $movimientos
     * @return \Illuminate\Http\Response
     */
    public function edit(Movimientos $movimientos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Movimientos  $movimientos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movimientos $movimientos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Movimientos  $movimientos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movimientos $movimientos)
    {
        //
    }
}
