<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Sede;
use App\User;
use App\Venta;
use App\Creditos;
use App\Recibos;
use DB;
use App\Clientes;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');


    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $usuarioSesion = $request->session()->get('key');
        $anioActual    = date("Y");

        // sede_id SIEMPRE desde la sesión (puede estar modificado por selección de admin)
        $sedeId = $usuarioSesion->sede_id;

        // Re-fetch solo para datos de perfil (nombre, imagen, etc.)
        $usuario        = User::find($usuarioSesion->id);
        $sedeDelUsuario = $usuarioSesion->sede ?? $usuario->sede;

        //OBTENER EL TOTAL DE LAS VENTAS AL CONTADO
        $totalVentasContado = $this->total_ventas_contado($sedeId);

        //OBTENER EL TOTAL DE VENTAS AL CREDITO
        $totalVentasCredito = $this->total_ventas_credito($sedeId);

        //DEVOLVER EL TOTAL DE CREDITOS ACTIVOS
        $totalCreditoActivo = $this->total_credito_activo($sedeId);

        //DEVOLVER EL TOTAL AMORITZADO
        $totalamortizado = $this->total_amortizado($sedeId);

        return view('home', compact('sedeDelUsuario','totalVentasContado','totalVentasCredito','totalCreditoActivo','totalamortizado','anioActual'));
    }



    public function total_ventas_contado($id_sede){
        $query = Venta::where('tipo_pago_id', '1');
        if ($id_sede != 1) {
            $query->where('sede_id', '=', $id_sede);
        }
        return $query->sum('monto');
    }

    public function total_ventas_credito($id_sede){
        $query = Venta::where('tipo_pago_id', '2');
        if ($id_sede != 1) {
            $query->where('sede_id', '=', $id_sede);
        }
        return $query->sum('monto');
    }

    public function total_credito_activo($id_sede){
        $query = Creditos::where('esta_cre', '=', '1');
        if ($id_sede != 1) {
            $query->where('sede_id', '=', $id_sede);
        }
        return $query->sum('mont_cre');
    }

    public function total_amortizado($id_sede){
        $query = Recibos::where('esta_rec', '=', 'EMITIDO');
        if ($id_sede != 1) {
            $query->where('sede_id', '=', $id_sede);
        }
        return $query->sum('mont_rec');
    }

    //TRAER LAS VENTAS POR AÑO
    public function ventasPorMesYAnio(Request $request){
        $usuario   = $request->session()->get('key');
        $anioActual = date("Y");

        $query = Venta::select(
            DB::raw('EXTRACT(YEAR FROM fecha) as anio'),
            DB::raw('EXTRACT(MONTH FROM fecha) as mes'),
            DB::raw('SUM(monto) as total')
        )
        ->where(DB::raw('EXTRACT(YEAR FROM fecha)'), '=', $anioActual);

        if ($usuario->sede_id != 1) {
            $query->where('sede_id', $usuario->sede_id);
        }

        $ventasPorMesYAnio = $query->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        $ventasPorMesYAnio = $this->ConversionMes($ventasPorMesYAnio);
        return response()->json($ventasPorMesYAnio);
    }

    //TRAER LA DATA TANTO VENTA AL CREDITO Y AL CONTADO
    public function VentasContadoCredito(Request $request){
        $usuario    = $request->session()->get('key');
        $anioActual = date("Y");

        $baseQuery = Venta::select(
            DB::raw('EXTRACT(YEAR FROM fecha) as anio'),
            DB::raw('EXTRACT(MONTH FROM fecha) as mes'),
            DB::raw('SUM(monto) as total')
        )->where(DB::raw('EXTRACT(YEAR FROM fecha)'), '=', $anioActual);

        $qContado = clone $baseQuery;
        $qCredito = clone $baseQuery;

        if ($usuario->sede_id != 1) {
            $qContado->where('sede_id', $usuario->sede_id);
            $qCredito->where('sede_id', $usuario->sede_id);
        }

        $ventasContado = $qContado->where('tipo_pago_id', 1)
            ->groupBy('anio', 'mes')->orderBy('anio', 'asc')->orderBy('mes', 'asc')->get();

        $ventasCredito = $qCredito->where('tipo_pago_id', 2)
            ->groupBy('anio', 'mes')->orderBy('anio', 'asc')->orderBy('mes', 'asc')->get();

        $ventasContado = $this->ConversionMes($ventasContado);
        $ventasCredito = $this->ConversionMes($ventasCredito);

        return response()->json(['contado' => $ventasContado, 'credito' => $ventasCredito]);
    }

    //PASAR LA DATA DE MESES A TEXTO
    public function ConversionMes($data) {
        $meses = [
            1 => 'Enero',    2 => 'Febrero',   3 => 'Marzo',
            4 => 'Abril',    5 => 'Mayo',       6 => 'Junio',
            7 => 'Julio',    8 => 'Agosto',     9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        foreach ($data as $venta) {
            $venta->mes = $meses[(int)$venta->mes];
        }
        return $data;
    }

    //FUNCIÓN PARA PODER TRAER LOS 5 PRODUCTOS MÁS VENDIDOS
    public function top5ProductosMasVendidos(){
        $topProductos = DB::table('detalle_venta')
            ->select('producto_id', DB::raw('SUM(cantidad) as total_unidades_vendidas'))
            ->groupBy('producto_id')
            ->orderByDesc('total_unidades_vendidas')
            ->limit(5)
            ->get();

        $topProductosIds = $topProductos->pluck('producto_id')->toArray();

        $productosMasVendidos = DB::table('productos')
            ->join('detalle_venta', 'productos.id', '=', 'detalle_venta.producto_id')
            ->whereIn('productos.id', $topProductosIds)
            ->select('productos.nomb_pro as nombre_producto', DB::raw('SUM(detalle_venta.cantidad) as total_unidades_vendidas'))
            ->groupBy('productos.id', 'productos.nomb_pro')
            ->get();

        return response()->json($productosMasVendidos);
    }

    //FUNCIONES DEL TOP 10 DE CLIENTES MÁS DEUDORES
    public function top10ClientesMasDeudores(Request $request){
        $usuario = $request->session()->get('key');

        $query = Creditos::select('cliente_id', DB::raw('SUM(mont_cre) as total_deuda'))
            ->where('esta_cre', '=', 1);

        if ($usuario->sede_id != 1) {
            $query->where('sede_id', '=', $usuario->sede_id);
        }

        $topClientes    = $query->groupBy('cliente_id')->orderByDesc('total_deuda')->limit(10)->get();
        $topClientesIds = $topClientes->pluck('cliente_id')->toArray();

        $clientesMasDeudores = DB::table('clientes')
            ->join('creditos', 'clientes.id', '=', 'creditos.cliente_id')
            ->whereIn('clientes.id', $topClientesIds)
            ->select('clientes.razon_social as nombre_cliente', DB::raw('SUM(creditos.mont_cre) as total_deuda'))
            ->groupBy('clientes.id', 'clientes.razon_social')
            ->get();

        return response()->json($clientesMasDeudores);
    }

    public function top10ClientesMasCompraron(Request $request)
    {
        $usuario = $request->session()->get('key');

        $query = Venta::select('cliente_id', DB::raw('SUM(monto) as total_compras'))
            ->where('tipo_pago_id', 1);

        if ($usuario->sede_id != 1) {
            $query->where('sede_id', '=', $usuario->sede_id);
        }

        $topClientes = $query->groupBy('cliente_id')
            ->orderByDesc('total_compras')
            ->limit(10)
            ->pluck('cliente_id')
            ->toArray();

        $clientesMasCompraron = DB::table('clientes')
            ->whereIn('clientes.id', $topClientes)
            ->where('clientes.razon_social', '<>', null)
            ->select('clientes.razon_social as nombre_cliente', DB::raw('SUM(ventas.monto) as total_compras'))
            ->join('ventas', 'clientes.id', '=', 'ventas.cliente_id')
            ->groupBy('clientes.id', 'razon_social')
            ->orderByDesc('total_compras')
            ->get();

        return response()->json($clientesMasCompraron);
    }

   //FUNCION PARA PODER DEVOLVER LOS MEJORES COBRADORES

}
