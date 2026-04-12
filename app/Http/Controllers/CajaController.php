<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Caja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Venta;

use App\Http\Controllers\servicios\FuncionesController;

class CajaController extends Controller
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
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $ver_caja = Caja::where('user_id', '=', $user_id)->where('tipo_envio', '=', $envio)->where('sede_id', '=', $idsede)->orderBy('id', 'desc')->limit(1)->first();

        if ($ver_caja) {
            $estado_caja = $ver_caja->estado;
            $fecha = date('d-m-Y', strtotime($ver_caja->fecha_apertura));
            $total_fisico = $servicios->total_caja_fisica($ver_caja->id);
            $total_virtual = $servicios->total_caja_virtual($ver_caja->id);
            $total_ingresos_fisico = $servicios->total_ingresos_caja_fisica($ver_caja->id);
            $total_egresos_fisico = $servicios->total_egresos_caja_fisica($ver_caja->id);;
            $total_ingresos_virtual = $servicios->total_ingresos_caja_virtual($ver_caja->id);
            $total_egresos_virtual = $servicios->total_ingresos_caja_virtual($ver_caja->id);
            $idcaja = $ver_caja->id;
        } else {
            $estado_caja = 0;
            $fecha = "00-00-0000";
            $total_fisico = "0.00";
            $total_virtual = "0.00";
            $total_ingresos_fisico = "0.00";
            $total_egresos_fisico = "0.00";
            $total_ingresos_virtual = "0.00";
            $total_egresos_virtual = "0.00";
            $idcaja = 0;
        }

        return view('caja.index', compact('estado_caja', 'fecha', 'total_fisico', 'total_virtual', 'total_ingresos_fisico', 'total_egresos_fisico', 'total_ingresos_virtual', 'total_egresos_virtual', 'idcaja'));
    }

    //DEVOLVER LAS CANTIDAD DE TRANSCIONES REALIZADAS EN EL DÍA
    public function cantidadtransacciones()
    {

        $fecha = date('Y-m-d');
        $pago = Venta::where('fecha', '=', $fecha)->get();

        $total = 0;

        if (count($pago) == 0) {

            $total = 0;
        } else {
            $total = count($pago);
        }

        return response($total);
    }

    public function totaldiario(Request $request)
    {

        $usuario = $request->session()->get('key');
        //print_r($usuario);exit();
        $caja = Caja::where('estado', '=', 1)
            ->where('sede_id', '=', $usuario->sede_id)
            ->first();

        $total = 0;

        if ($caja == "") {

            $total = 0;
        } else {


            $totalcaja = DB::table('caja as c')
                ->where('c.estado', '=', 1)
                ->select(DB::raw('SUM(c.monto_apertura) as apertura'))
                ->where('c.sede_id', '=', $usuario->sede_id)
                ->get();

            $totalpago = DB::table('ventas as v')
                ->select(DB::raw('SUM(v.monto) as total'))
                ->where('v.venta_estado', '=', 1)
                ->where('v.sede_id', '=', $usuario->sede_id)
                ->where('v.fecha', '>=', $caja->fecha_apertura)
                ->get();

            $total = $totalpago[0]->total + $totalcaja[0]->apertura;
        }





        return response($total);
    }

    public function show($id)
    {
    }


    //METODO PARA VERIFICAR SI YA SE ABRIO LA CAJA
    public function validar_caja()
    {
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $ver_caja = Caja::where('user_id', '=', $user_id)->where('tipo_envio', '=', $envio)->where('sede_id', '=', $idsede)->orderBy('id', 'desc')->limit(1)->first();

        $mensaje = "";
        $status = 0;

        if (!$ver_caja) {
            $mensaje = "Usted no abrio caja";
            $status = 1;

            $json = array(
                "mensaje" => $mensaje,
                "status" => $status
            );

            return response()->json($json);
        }

        $estado_caja = $ver_caja->estado;

        $fecha_actual = date('Y-m-d');

        $fecha_apertura = $ver_caja->fecha_apertura;

        if ($fecha_apertura == $fecha_actual) {
            if($estado_caja == 0) {
                $mensaje = "Usted no abrio caja";
                $status = 1;
            }
        } else {
            $mensaje = "Usted tiene una caja anterior abierta, por favor cierre caja";
            $status = 1;
        }

        $json = array(
            "mensaje" => $mensaje,
            "status" => $status
        );

        return response()->json($json);


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
        $this->validate($request, [
            'monto_aperturar' => 'required'
        ]);

        $user = Auth::user();
        $hora = date("H:i:s");
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $caja = new Caja;
        $caja->sede_id = $idsede;
        $caja->name_usuario = $user->name;
        $caja->fecha_apertura =$request->fecha_apertura; //date('Y-m-d');
        $caja->hora_apertura = $hora;
        $caja->monto_apertura = $request->monto_aperturar;
        $caja->estado = 1;
        $caja->tipo_envio = $envio;
        $caja->user_id = $user_id;

        $caja->save();

        return response()->json('OK');
    }

    public function cerrar_caja($idcaja)
    {
        $servicios = new FuncionesController;
        $caja = Caja::where('id', '=', $idcaja)->first();

        $total_fisico = $servicios->total_caja_fisica($idcaja);
        $total_virtual = $servicios->total_caja_virtual($idcaja);

        $hora = date("H:i:s");

        $caja->estado = 0;
        $caja->monto_cierre_fisico = $total_fisico;
        $caja->monto_cierre_virtual = $total_virtual;
        $caja->fecha_cierre =date('Y-m-d');
        $caja->hora_cierre = $hora;

        $caja->save();

        return response()->json('ok');
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
        $this->validate($request, [
            'monto_cierre' => 'required'
        ]);



        $hora = date("H:i:s");

        $caja = Caja::where('id', '=', $request->id)->first();
        $caja->fecha_cierre =$caja->fecha_apertura;// date('Y-m-d');
        $caja->hora_cierre = $hora;
        $caja->monto_cierre = $request->monto_cierre;
        $caja->estado = 0;
        $caja->save();
        return response()->json('OK');
    }
}
