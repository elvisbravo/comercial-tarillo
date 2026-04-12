<?php

namespace App\Http\Controllers;
use App\Tipo_comprobantes;
use App\Sede;
use App\Venta;
use App\User;

use App\Http\Controllers\servicios\FuncionesController;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ReporteAllVentasController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    


    public function index(){
        $sedes = Sede::all();
        return view('reporteventas.sucursales', compact('sedes'));
    }

    public function tipocomprobantes(){
        $tipo_comprobantes = DB::table('tipo_comprobantes')->whereIn('descripcion',['BOLETA DE VENTA ELECTRONICA', 'FACTURA ELECTRONICA', 'NOTA DE CREDITO ELECTRONICA'])->get();

        return response()->json($tipo_comprobantes);
    }

    public function sede(){

        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $sede= DB::table('users')
        ->join('sedes', 'users.sede_id', '=', 'sedes.id')
        ->select('sedes.nombre', 'sedes.id')
        ->where('users.sede_id','=',$idsede)
        ->where('users.id', '=', $user_id)->get();
        
            return response()->json($sede);
    }

    public function consulta(Request $request){
        $idsede = $request->sucursal;

        $desde = $request->desde;
        $hasta = $request->hasta;
        $comprobante = $request->tipo_comprobante;

        $servicios = new FuncionesController;

        $envio = $servicios->tipo_envio_sunat();

        if ($idsede == 1) {
            if ($comprobante == 0) {
                $ventas = DB::table('ventas')
                    ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                    ->join('tipo_comprobantes', 'ventas.tipo_comprobante_id', '=', 'tipo_comprobantes.id')
                    ->select('clientes.nomb_per', 'clientes.pate_per', 'clientes.mate_per','clientes.razon_social', 'clientes.documento', 'tipo_comprobantes.descripcion as comprobante', 'ventas.id', DB::raw("to_char(ventas.fecha, 'DD-MM-YYYY') as fecha"), 'ventas.hora', 'ventas.serie_comprobante', 'ventas.numero_comprobante', 'ventas.monto', 'ventas.sede_id', 'ventas.venta_estado', 'ventas.aceptado_sunat', 'ventas.mensaje_sunat','ventas.tipo_comprobante_id', 'ventas.estado_nota','ventas.serie_nota_credito','ventas.numero_nota_credito', DB::raw("to_char(ventas.fecha_eliminacion, 'DD-MM-YYYY') as fecha_eliminacion"))
                    ->where('ventas.tipo_envio', '=', $envio)
                    ->whereBetween('ventas.fecha', [$desde, $hasta])
                    ->whereIn('ventas.tipo_comprobante_id', [1,2,3])
                    ->orderBy('ventas.sede_id', 'asc')
                    ->orderBy('ventas.tipo_comprobante_id', 'asc')
                    ->orderBy('ventas.id', 'asc')
                    ->get();
            } else {
                $ventas = DB::table('ventas')
                    ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                    ->join('tipo_comprobantes', 'ventas.tipo_comprobante_id', '=', 'tipo_comprobantes.id')
                    ->select('clientes.nomb_per', 'clientes.pate_per', 'clientes.mate_per','clientes.razon_social', 'clientes.documento', 'tipo_comprobantes.descripcion as comprobante', 'ventas.id', DB::raw("to_char(ventas.fecha, 'DD-MM-YYYY') as fecha"), 'ventas.hora', 'ventas.serie_comprobante', 'ventas.numero_comprobante', 'ventas.monto', 'ventas.sede_id', 'ventas.venta_estado', 'ventas.aceptado_sunat', 'ventas.mensaje_sunat','ventas.tipo_comprobante_id', 'ventas.estado_nota','ventas.serie_nota_credito','ventas.numero_nota_credito')
                    ->where('ventas.tipo_envio', '=', $envio)
                    ->where('ventas.tipo_comprobante_id', $comprobante)
                    ->whereBetween('ventas.fecha', [$desde, $hasta])
                    ->orderBy('ventas.sede_id', 'asc')
                    ->orderBy('ventas.id', 'asc')
                    ->get();
            }
        } else {
            if ($comprobante == 0) {
                $ventas = DB::table('ventas')
                    ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                    ->join('tipo_comprobantes', 'ventas.tipo_comprobante_id', '=', 'tipo_comprobantes.id')
                    ->select('clientes.nomb_per', 'clientes.pate_per', 'clientes.mate_per','clientes.razon_social', 'clientes.documento', 'tipo_comprobantes.descripcion as comprobante', 'ventas.id', DB::raw("to_char(ventas.fecha, 'DD-MM-YYYY') as fecha"), 'ventas.hora', 'ventas.serie_comprobante', 'ventas.numero_comprobante', 'ventas.monto', 'ventas.sede_id', 'ventas.venta_estado', 'ventas.aceptado_sunat', 'ventas.mensaje_sunat','ventas.tipo_comprobante_id', 'ventas.estado_nota','ventas.serie_nota_credito','ventas.numero_nota_credito', DB::raw("to_char(ventas.fecha_eliminacion, 'DD-MM-YYYY') as fecha_eliminacion"))
                    ->where('ventas.tipo_envio', '=', $envio)
                    ->where('ventas.sede_id', '=', $idsede)
                    ->whereBetween('ventas.fecha', [$desde, $hasta])
                    ->whereIn('ventas.tipo_comprobante_id', [1,2,3])
                    ->orderBy('ventas.tipo_comprobante_id', 'asc')
                    ->orderBy('ventas.id', 'asc')
                    ->get();
            } else {
                $ventas = DB::table('ventas')
                    ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                    ->join('tipo_comprobantes', 'ventas.tipo_comprobante_id', '=', 'tipo_comprobantes.id')
                    ->select('clientes.nomb_per', 'clientes.pate_per', 'clientes.mate_per','clientes.razon_social', 'clientes.documento', 'tipo_comprobantes.descripcion as comprobante', 'ventas.id', DB::raw("to_char(ventas.fecha, 'DD-MM-YYYY') as fecha"), 'ventas.hora', 'ventas.serie_comprobante', 'ventas.numero_comprobante', 'ventas.monto', 'ventas.sede_id', 'ventas.venta_estado', 'ventas.aceptado_sunat', 'ventas.mensaje_sunat','ventas.tipo_comprobante_id', 'ventas.estado_nota','ventas.serie_nota_credito','ventas.numero_nota_credito')
                    ->where('ventas.tipo_envio', '=', $envio)
                    ->where('ventas.sede_id', '=', $idsede)
                    ->where('ventas.tipo_comprobante_id', $comprobante)
                    ->whereBetween('ventas.fecha', [$desde, $hasta])
                    ->orderBy('ventas.id', 'asc')
                    ->get();
            }
        }

        return response()->json($ventas);

    }
}
