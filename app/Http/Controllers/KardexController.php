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
        $origen = Almacen::where('sede_id', '=', $idsede)->where('estado', '=', 1)->get();
        return view('kardex.index', compact('origen'));
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
            $productos = Productos::where('nomb_pro', 'ilike', '%' . $request->q . '%')->get();
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
            ->leftJoin('tipo_comprobantes', 'kardexes.tipo_comprobante', '=', 'tipo_comprobantes.id')
            ->leftJoin('stock_location', 'kardexes.ubicacion_id', '=', 'stock_location.id')
            ->select('kardexes.*', 'tipo_comprobantes.descripcion as comprobante', 'stock_location.name as nombre_ubicacion')
            ->where('kardexes.producto_id', $request->producto)
            ->where('kardexes.tipo_envio', $envio)
            ->whereBetween('kardexes.fecha', [$request->fecha_inicio, $request->fecha_final]);

        if ($request->has('ubicacion') && $request->ubicacion != 'todas') {
            $query->where('kardexes.ubicacion_id', $request->ubicacion);
        } else {
            $query->where('stock_location.almacen_id', $request->almacen);
        }

        $kardex = $query->orderBy('kardexes.fecha', 'desc')
            ->orderBy('kardexes.id', 'desc')
            ->get();

        return response()->json($kardex);
    }

    public function updateKardex()
    {
        $lista = DB::table('kardexes')
            ->where('fecha_comprobante', null)
            ->where('tipo_comprobante', null)
            ->get();

        foreach ($lista as $key => $value) {
            $fecha_comprobante = $value->fecha;

            if ($value->serie_comprobante === 'NC01' || $value->serie_comprobante === 'NC03') {
                $tipo_comprobante = 12;
            } else {
                $tipo_comprobante = 5;
            }

            $update = Kardex::find($value->id);
            $update->fecha_comprobante = $fecha_comprobante;
            $update->tipo_comprobante = $tipo_comprobante;

            $update->save();
        }
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

    public function recalculateKardex()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        try {
            DB::beginTransaction();

            $items = DB::table('kardexes')
                ->select('producto_id', 'ubicacion_id', 'tipo_envio')
                ->groupBy('producto_id', 'ubicacion_id', 'tipo_envio')
                ->get();

            foreach ($items as $item) {
                $movimientos = Kardex::where('producto_id', $item->producto_id)
                    ->where('ubicacion_id', $item->ubicacion_id)
                    ->where('tipo_envio', $item->tipo_envio)
                    ->orderBy('fecha', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                $running_cantidad = 0;
                $running_subtotal = 0;
                $current_avg_price = 0;

                foreach ($movimientos as $mov) {
                    $tipo = (int)$mov->tipo;
                    $cantidad = (float)$mov->cantidad_unitaria;

                    // Identificar traslados por prefijo 'GI' (case-insensitive) o por tipo de comprobante 7
                    $isTraslado = ($mov->serie_comprobante && stripos(trim($mov->serie_comprobante), 'GI') === 0) || ($mov->tipo_comprobante == 7);

                    if ($isTraslado) {
                        $traslado = DB::table('traslados')
                            ->where('serie', trim($mov->serie_comprobante))
                            ->where('correlativo', trim($mov->correlativo_comprobante))
                            ->first();

                        if ($traslado) {
                            // REQUERIMIENTO: Solo se considera si el estado es exactamente 0 (Totalmente recibido)
                            if ($traslado->estado == 0) {
                                // Buscamos la cantidad recibida real en el detalle del traslado
                                $detalleTraslado = DB::table('detalle_traslado')
                                    ->where('traslado_id', $traslado->id)
                                    ->where('producto_id', $mov->producto_id)
                                    ->first();

                                if ($detalleTraslado) {
                                    $cantidad = (float)$detalleTraslado->cantidad_recibido;
                                }
                                // Si no hay detalle, se mantiene la cantidad original del movimiento
                            } else {
                                // Si el estado es diferente de 0 (ej. 1=Creado o 2=Parcial), NO se considera en el kardex
                                $cantidad = 0;
                            }
                        } else {
                            // Si es un documento marcado como traslado pero no existe en la tabla traslados, lo ignoramos
                            $cantidad = 0;
                        }

                        // Actualizamos el objeto movimiento con la cantidad decidida
                        $mov->cantidad_unitaria = $cantidad;
                        // Si es un ingreso, recalculamos el subtotal unitario con la nueva cantidad
                        if ($tipo === 1) {
                            $mov->subtotal_unitario = $cantidad * (float)$mov->precio_unitario;
                        }
                    }

                    if ($tipo === 1) { // Ingreso
                        $running_cantidad += $cantidad;
                        $running_subtotal += (float)$mov->subtotal_unitario;
                    } else if ($tipo === 2) { // Salida
                        $mov->subtotal_unitario = $current_avg_price * $cantidad;
                        $running_cantidad -= $cantidad;
                        $running_subtotal -= (float)$mov->subtotal_unitario;
                    }

                    if ($cantidad != 0) {
                        if ($running_cantidad <= 0) {
                            $running_subtotal = 0;
                            $current_avg_price = 0;
                            $running_cantidad = ($running_cantidad < 0 && $tipo === 2) ? $running_cantidad : 0;
                        } else {
                            $current_avg_price = $running_subtotal / $running_cantidad;
                        }
                    } else {
                        // Si la cantidad es 0 (movimiento ignorado), mantenemos el saldo anterior 
                        // evitando el reset agresivo a 0 que descuadra el historial negativo.
                        if ($running_cantidad > 0) {
                            $current_avg_price = $running_subtotal / $running_cantidad;
                        }
                    }

                    $mov->cantidad_total = $running_cantidad;
                    $mov->precio_total = $current_avg_price;
                    $mov->subtotal_total = $running_subtotal;
                    $mov->save();

                    if ($mov->producto_id == 6) {
                        \Log::info("Recalculando Producto 6: ID {$mov->id}, Tipo {$tipo}, Cant {$cantidad}, Total Acumulado {$running_cantidad}");
                    }
                }

                DB::table('detalle_almacen_productos')
                    ->where('producto_id', $item->producto_id)
                    ->where('ubicacion_id', $item->ubicacion_id)
                    ->where('tipo_envio', $item->tipo_envio)
                    ->update(['stock' => $running_cantidad]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Kardex y Stock actualizados correctamente. Se ha generado un registro en el log para el producto 6.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al recalcular: ' . $e->getMessage()
            ], 500);
        }
    }
}
