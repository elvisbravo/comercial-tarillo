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

        $usuario = $request->session()->get('key');
        $anioActual = date("Y");

        $usuario = User::find($usuario->id);
        $sedeDelUsuario = $usuario->sede;

        //OBTENER EL TOTAL DE LAS VENTAS AL CONTADO
        $totalVentasContado=$this->total_ventas_contado($usuario->sede_id);

        //OBTENER EL TOTAL DE VENTAS AL CREDITO
        $totalVentasCredito=$this->total_ventas_credito($usuario->sede_id);

        //DEVOLVER EL TOTAL DE CREDITOS ACTIVOS
        $totalCreditoActivo=$this->total_credito_activo($usuario->sede_id);

        //DEVOLVER EL TOTAL AMORITZADO
        $totalamortizado=$this->total_amortizado($usuario->sede_id);




       //return response()->json($ventasPorMesYAnio);


        return view('home',compact('sedeDelUsuario','totalVentasContado','totalVentasCredito','totalCreditoActivo','totalamortizado','anioActual'));
    }



    public function total_ventas_contado($id_sede){

        $totalVentasContado = Venta::where('tipo_pago_id', '1')
        ->where('sede_id','=',$id_sede)
        ->sum('monto');

        return $totalVentasContado;


    }

    public function total_ventas_credito($id_sede){

        $totalVentasCredito = Venta::where('tipo_pago_id', '2')
        ->where('sede_id','=',$id_sede)
        ->sum('monto');

        return $totalVentasCredito;

    }


    public function total_credito_activo($id_sede){

        $totalCreditoActivo=Creditos::where('esta_cre','=','1')
        ->where('sede_id','=',$id_sede)
        ->sum('mont_cre');


        return $totalCreditoActivo;


    }

    public function total_amortizado($id_sede){

        $totalamortizado=Recibos::where('esta_rec','=','EMITIDO')
        ->where('sede_id','=',$id_sede)
        ->sum('mont_rec');

        return $totalamortizado;


    }

    //TRAER LAS VENTAS POR AÑO

    public function ventasPorMesYAnio(Request $request){


        $usuario = $request->session()->get('key');

          // Obtener el año actual
         $anioActual = date("Y");



        $ventasPorMesYAnio = Venta::select(
            DB::raw('EXTRACT(YEAR FROM fecha) as anio'),
            DB::raw('EXTRACT(MONTH FROM fecha) as mes'),
            DB::raw('SUM(monto) as total')
        )
        ->where('sede_id', $usuario->sede_id)
        ->where(DB::raw('EXTRACT(YEAR FROM fecha)'), '=', $anioActual)
        ->groupBy('anio', 'mes')
        ->orderBy('anio', 'asc')
        ->orderBy('mes', 'asc')
        ->get();

        // Mapear los números de mes a los nombres correspondientes
        $ventasPorMesYAnio = $this->ConversionMes($ventasPorMesYAnio);

        return response()->json($ventasPorMesYAnio);



    }

   //TRAER LA DATA TANTO VENTA AL CREDITO Y AL CONTADO
   public function VentasContadoCredito(Request $request){

            $usuario = $request->session()->get('key');

            // Obtener el año actual
            $anioActual = date("Y");

            // Obtener ventas al contado
            $ventasContado = Venta::select(
                DB::raw('EXTRACT(YEAR FROM fecha) as anio'),
                DB::raw('EXTRACT(MONTH FROM fecha) as mes'),
                DB::raw('SUM(monto) as total')
            )
            ->where('sede_id', $usuario->sede_id)
            ->where('tipo_pago_id', 1) // Tipo de pago al contado
            ->where(DB::raw('EXTRACT(YEAR FROM fecha)'), '=', $anioActual)
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

            // Obtener ventas al crédito
            $ventasCredito = Venta::select(
                DB::raw('EXTRACT(YEAR FROM fecha) as anio'),
                DB::raw('EXTRACT(MONTH FROM fecha) as mes'),
                DB::raw('SUM(monto) as total')
            )
            ->where('sede_id', $usuario->sede_id)
            ->where('tipo_pago_id', 2) // Tipo de pago al crédito
            ->where(DB::raw('EXTRACT(YEAR FROM fecha)'), '=', $anioActual)
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

            // Llamar a la función ConversionMes para convertir los meses a texto en cada conjunto de datos
            $ventasContado = $this->ConversionMes($ventasContado);
            $ventasCredito = $this->ConversionMes($ventasCredito);

            // Combinar los resultados en un solo conjunto de datos
            $ventasPorMesYAnio = [
                'contado' => $ventasContado,
                'credito' => $ventasCredito,
            ];

            return response()->json($ventasPorMesYAnio);


   }




    //PASAR LA DATA DE MESES A TEXTO
    public function ConversionMes($data) {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        // Transformar los números de mes a nombres
        foreach ($data as $venta) {
            $mes = (int)$venta->mes;
            $venta->mes = $meses[$mes];
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

         // Obtener los datos de los productos más vendidos
    $topProductosIds = $topProductos->pluck('producto_id')->toArray();

     // Consultar los detalles de los productos más vendidos
     $productosMasVendidos = DB::table('productos')
        ->join('detalle_venta', 'productos.id', '=', 'detalle_venta.producto_id')
        ->whereIn('productos.id', $topProductosIds)
        ->select('productos.nomb_pro as nombre_producto', DB::raw('SUM(detalle_venta.cantidad) as total_unidades_vendidas'))
        ->groupBy('productos.id', 'productos.nomb_pro')
        ->get();

       // Aquí puedes devolver la información como prefieras
    return response()->json($productosMasVendidos);





   }

   //FUNCIONES DEL TOP 10 DE CLIENTES MÁS DEUDORES
   public function top10ClientesMasDeudores(Request $request){

      $usuario = $request->session()->get('key');

      $topClientes = Creditos::select('cliente_id', DB::raw('SUM(mont_cre) as total_deuda'))
        ->where('esta_cre', '=', 1)
        ->where('sede_id', '=', $usuario->sede_id)
        ->groupBy('cliente_id')
        ->orderByDesc('total_deuda')
        ->limit(10)
        ->get();

           // Obtener los IDs de los clientes más deudores
        $topClientesIds = $topClientes->pluck('cliente_id')->toArray();

        //CONSULTAR LOS DETALLES DE LA DATA
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

        $topClientes = Venta::select('cliente_id', DB::raw('SUM(monto) as total_compras'))
        ->where('sede_id', '=', $usuario->sede_id)
        ->where('tipo_pago_id', 1)
        ->groupBy('cliente_id')
        ->orderByDesc('total_compras')
        ->limit(10)
        ->pluck('cliente_id')
        ->toArray();

        $clientesMasCompraron = DB::table('clientes')
        ->whereIn('clientes.id', $topClientes)
        ->where('clientes.razon_social','<>',null)
        ->select('clientes.razon_social as nombre_cliente', DB::raw('SUM(ventas.monto) as total_compras'))
        ->join('ventas', 'clientes.id', '=', 'ventas.cliente_id')
        ->groupBy('clientes.id', 'razon_social')
        ->orderByDesc('total_compras')
        ->get();


        return response()->json($clientesMasCompraron);



   }

   //FUNCION PARA PODER DEVOLVER LOS MEJORES COBRADORES




}
