<?php

namespace App\Http\Controllers\servicios;

use App\Kardex;
use App\Correlativos;
use App\Sede;
use App\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Detalle_almacen_productos;
use App\Movimientos;
use App\detalle_alamcen_producto;
use Illuminate\Support\Facades\Log;

class FuncionesController
{

    public function movimiento_kardex_producto($idubicacion, $idproducto, $cantidad, $tipo, $descripcion, $serie, $correlativo, $precio_unitario, $tipo_comprobante, $fecha, $fecha_comprobante)
    {
        //DB::beginTransaction();

        //try{
        //tipo entrada de almacen 1, salida de almacen 2
        $repuesta = "";
        $kardex = Kardex::where('producto_id', '=', $idproducto)->where('ubicacion_id', '=', $idubicacion)->get();

        $envio = $this->tipo_envio_sunat();

        if (count($kardex) == 0) {
            if ($tipo == 1) {
                $total = $cantidad * $precio_unitario;


                $des = "";

                if ($serie == "") {
                    $des = "Ingreso Inicial";
                } else {
                    $des = $descripcion;
                }


                $kar = new Kardex;
                $kar->producto_id = $idproducto;
                $kar->ubicacion_id = $idubicacion;
                $kar->fecha = $fecha;
                $kar->descripcion = $des;
                $kar->tipo = $tipo;
                $kar->serie_comprobante = $serie;
                $kar->correlativo_comprobante = $correlativo;
                $kar->cantidad_unitaria = $cantidad;
                $kar->precio_unitario = $precio_unitario;
                $kar->subtotal_unitario = $total;
                $kar->cantidad_total = $cantidad;
                $kar->precio_total = $precio_unitario;
                $kar->subtotal_total = $total;
                $kar->tipo_envio = $envio;
                $kar->fecha_comprobante = $fecha_comprobante;
                $kar->tipo_comprobante = $tipo_comprobante;
                $kar->estado = 1;
                $kar->save();
                $repuesta = 'OK';
            }

            return response()->json($repuesta);
        } else {

            $kardexes = Kardex::where('producto_id', '=', $idproducto)->where('ubicacion_id', '=', $idubicacion)->orderBy('id', 'desc')->get();

            $ultimo = $kardexes->last();

            $kar = Kardex::find($ultimo->id);

            $stock_total = $kar->cantidad_total;
            $subtotal_total = $kar->subtotal_total;
            $ultimo_costo = $kar->precio_total;
            $cantidad_unitario = $cantidad;
            $precio_unitario = $precio_unitario;

            if ($tipo == 1) {
                $subtotal_unitaria = $cantidad_unitario * $precio_unitario;
                $cantidad_total = $stock_total + $cantidad_unitario;
                $total_subtotal = $subtotal_total + $subtotal_unitaria;
                $precio_promedio = $total_subtotal / $cantidad_total;

                $kardex = new Kardex;

                $kardex->producto_id = $idproducto;
                $kardex->ubicacion_id = $idubicacion;
                $kardex->fecha = $fecha;
                $kardex->descripcion = $descripcion;
                $kardex->tipo = $tipo;
                $kardex->serie_comprobante = $serie;
                $kardex->correlativo_comprobante = $correlativo;
                $kardex->cantidad_unitaria = $cantidad_unitario;
                $kardex->precio_unitario = $precio_unitario;
                $kardex->subtotal_unitario = $subtotal_unitaria;
                $kardex->cantidad_total = $cantidad_total;
                $kardex->precio_total = $precio_promedio;
                $kardex->subtotal_total = $total_subtotal;
                $kardex->tipo_envio = $envio;
                $kar->fecha_comprobante = $fecha_comprobante;
                $kar->tipo_comprobante = $tipo_comprobante;
                $kardex->estado = 1;
                $kardex->save();
                $repuesta = 'OK';
            } else {
                $subtotal_unitario = $ultimo_costo * $cantidad_unitario;
                $cantidad_total = $stock_total - $cantidad_unitario;
                $total_subtotal = $subtotal_total - $subtotal_unitario;

                $kardex = new Kardex;

                $kardex->producto_id = $idproducto;
                $kardex->ubicacion_id = $idubicacion;
                $kardex->fecha = $fecha;
                $kardex->descripcion = $descripcion;
                $kardex->tipo = $tipo;
                $kardex->serie_comprobante = $serie;
                $kardex->correlativo_comprobante = $correlativo;
                $kardex->cantidad_unitaria = $cantidad_unitario;
                $kardex->precio_unitario = $precio_unitario;
                $kardex->subtotal_unitario = $subtotal_unitario;
                $kardex->cantidad_total = $cantidad_total;
                $kardex->precio_total = $ultimo_costo;
                $kardex->subtotal_total = $total_subtotal;
                $kar->fecha_comprobante = $fecha_comprobante;
                $kar->tipo_comprobante = $tipo_comprobante;
                $kardex->tipo_envio = $envio;
                $kardex->estado = 1;
                $kardex->save();
                $repuesta = 'OK';
            }

            DB::commit();

            return response()->json($repuesta);
        }

        //}catch (Exception $e) {

        // return  response()->json($e);

        // }

    }

    public function tipo_envio_sunat()
    {
        $idsede = session('key')->sede_id;

        $sede = Sede::find($idsede);

        return $sede->tipo_envio;
    }

    //FUNCION PARA GUARDAR EL DETALLE DEL ALMACEN
    public function detalle_alamcen_producto($stock, $tipo_envio, $producto_id, $tipo, $ubicacion_id)
    {
        //DB::beginTransaction();

        $repuesta = "";
        try {

            if ($tipo == 0) {

                if ($this->validarstock($producto_id,  $ubicacion_id) == 0) {

                    $detalle = new Detalle_almacen_productos;
                    $detalle->stock = $stock;
                    $detalle->tipo_envio = $tipo_envio;
                    //$detalle->almacen_id = $almacen_id;
                    $detalle->producto_id = $producto_id;
                    $detalle->ubicacion_id = $ubicacion_id;
                    $detalle->save();
                    $repuesta = 'OK';
                } else if ($this->validarstock($producto_id, $ubicacion_id) == 1) {

                    $detalle = Detalle_almacen_productos::where('id', '=', $this->iddetalleproductos($producto_id, $ubicacion_id))->first();
                    $detalle->stock = $stock;
                    $detalle->ubicacion_id = $ubicacion_id;
                    $detalle->save();

                    $repuesta = 'OK';
                } else if ($this->validarstock($producto_id, $ubicacion_id) == 2) {

                    $detalle = Detalle_almacen_productos::where('id', '=', $this->iddetalleproductos($producto_id, $ubicacion_id))->first();
                    $detalle->stock = (int) $this->sacarstock($producto_id, $ubicacion_id) + (int)$stock;
                    $detalle->ubicacion_id = $ubicacion_id;
                    $detalle->save();
                    $repuesta = $detalle; //'OK';


                }
                DB::commit();
                return response()->json($repuesta);
            } else if ($tipo == 1) {

                if ($this->validarstock($producto_id, $ubicacion_id) == 0) {

                    $repuesta = 'ERROR';
                } else if ($this->validarstock($producto_id,  $ubicacion_id) == 1) {

                    $repuesta = 'ERROR';
                } else if ($this->validarstock($producto_id,  $ubicacion_id) == 2) {

                    $detalle = Detalle_almacen_productos::where('id', '=', $this->iddetalleproductos($producto_id,  $ubicacion_id))->first();
                    $detalle->stock = (int) $this->sacarstock($producto_id,  $ubicacion_id) - (int)$stock;
                    $detalle->save();
                    $repuesta = 'OK';
                }
            }
            //DB::commit();

        } catch (\Exception $e) {

            return  response()->json($e);
        }
    }


    //FUNCION PARA VALIDAR SI TIENES STOCK

    public function validarstock($producto_id,  $ubicacion_id)
    {

        $saldo = DB::table('detalle_almacen_productos as dt')
            ->select('dt.stock as stock')
            ->where([
                ['dt.producto_id', '=', $producto_id],
                ['dt.ubicacion_id', '=',  $ubicacion_id]
            ])
            ->first();

        if ($saldo == null) {

            return 0;
        } else if ($saldo->stock == 0) {

            return 1;
        } else {

            return 2;
        }
    }

    //FUNCION PARA TRAER EL ID DEL DETALLE ALMACEN PRODUCTO
    public function iddetalleproductos($producto_id, $ubicacion_id)
    {


        $saldo = DB::table('detalle_almacen_productos as dt')
            ->select('dt.id')
            ->where([
                ['dt.producto_id', '=', $producto_id],
                ['dt.ubicacion_id', '=', $ubicacion_id]
            ])
            ->first();

        return $saldo->id;
    }

    //FUNCION PARA SACAR EL STOCK DEL PRODUCTO
    public function sacarstock($producto_id, $ubicacion_id)
    {

        $saldo = DB::table('detalle_almacen_productos as dt')
            ->select('dt.stock')
            ->where([
                ['dt.producto_id', '=', $producto_id],
                ['dt.ubicacion_id', '=', $ubicacion_id]
            ])
            ->first();

        return $saldo->stock;
    }

    public function consultar_ruc_dni($tipo, $num_doc)
    {
        if ($tipo == 1) {

            //$ruta = "https://dniruc.apisperu.com/api/v1/dni/" . $num_doc ."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImN2ZWdhZ0Bob3RtYWlsLmNvbSJ9.pTHRdktUddFWRcSqrbi9CCRNDelFEfvHTD8Fa85Se5Q";
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://dniruc.apisperu.com/api/v1/dni/' . $num_doc . '?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImN2ZWdhZ0Bob3RtYWlsLmNvbSJ9.pTHRdktUddFWRcSqrbi9CCRNDelFEfvHTD8Fa85Se5Q',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response;
            $data = json_decode($response, true);
            //return $data;
            return response()->json($data);
        } elseif ($tipo == 6) {

            //$ruta = "https://dniruc.apisperu.com/api/v1/ruc/" . $num_doc . "?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImN2ZWdhZ0Bob3RtYWlsLmNvbSJ9.pTHRdktUddFWRcSqrbi9CCRNDelFEfvHTD8Fa85Se5Q";
            $curl = curl_init();


            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://dniruc.apisperu.com/api/v1/ruc/' . $num_doc . '?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImN2ZWdhZ0Bob3RtYWlsLmNvbSJ9.pTHRdktUddFWRcSqrbi9CCRNDelFEfvHTD8Fa85Se5Q',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $data = json_decode($response, true);

            return response()->json($data);
        } else {
            $json = array(
                "respuesta" => "error",
                "mensaje" => "Tipo de Documento Desconocido"
            );

            return response()->json($json);
        }

        /* $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $ruta,
            CURLOPT_USERAGENT => 'Consulta Datos',
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 400,
            CURLOPT_FAILONERROR => true
        ));

        $data = curl_exec($curl);

        if (curl_error($curl)) {
            $error_msg = curl_error($curl);
        }

        curl_close($curl);

        if (isset($error_msg)) {
            $resp['respuesta'] = 'error';
            $resp['titulo'] = 'Error';
            $resp['data'] = $data;
            $resp['encontrado'] = false;
            $resp['mensaje'] = 'Error en Api de Búsqueda';
            $resp['errores_curl'] = $error_msg;
            return $resp;
        }

        $data_resp = json_decode($data);

        $resp['respuesta'] = 'ok';
        $resp['encontrado'] = true;
        $resp['api'] = true;
        $resp['data'] = $data_resp;

        return response()->json($resp);*/
    }

    public function total_ingresos_caja_fisica($id_caja)
    {
        $idsede = session('key')->sede_id;
        $envio = $this->tipo_envio_sunat();

        $total = DB::table('movimientos')
            ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
            ->where('movimientos.estado', '=', 1)
            ->where('conceptos.tipo_movimiento', '=', 'INGRESO')
            ->where('movimientos.sede_id', '=', $idsede)
            ->where('movimientos.tipo_envio', '=', $envio)
            ->where('movimientos.tipo_caja', '=', 'FISICA')
            ->where('movimientos.id_sesion_caja', '=', $id_caja)
            ->sum('movimientos.monto');
        return number_format($total, 2, '.', '');
    }

    public function total_egresos_caja_fisica($id_caja)
    {
        $idsede = session('key')->sede_id;
        $envio = $this->tipo_envio_sunat();

        $total = DB::table('movimientos')
            ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
            ->where('movimientos.estado', '=', 1)
            ->where('conceptos.tipo_movimiento', '=', 'EGRESO')
            ->where('movimientos.sede_id', '=', $idsede)
            ->where('movimientos.tipo_envio', '=', $envio)
            ->where('movimientos.tipo_caja', '=', 'FISICA')
            ->where('movimientos.id_sesion_caja', '=', $id_caja)
            ->sum('movimientos.monto');
        return number_format($total, 2, '.', '');
    }

    public function total_caja_fisica($id_caja)
    {
        $caja = Caja::where('id', '=', $id_caja)->first();

        $total = $caja->monto_apertura + $this->total_ingresos_caja_fisica($id_caja) - $this->total_egresos_caja_fisica($id_caja);

        return number_format($total, 2, '.', '');
    }

    public function total_ingresos_caja_virtual($id_caja)
    {
        $idsede = session('key')->sede_id;
        $envio = $this->tipo_envio_sunat();

        $total = DB::table('movimientos')
            ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
            ->where('movimientos.estado', '=', 1)
            ->where('conceptos.tipo_movimiento', '=', 'INGRESO')
            ->where('movimientos.sede_id', '=', $idsede)
            ->where('movimientos.tipo_envio', '=', $envio)
            ->where('movimientos.tipo_caja', '=', 'VIRTUAL')
            ->where('movimientos.id_sesion_caja', '=', $id_caja)
            ->sum('movimientos.monto');
        return number_format($total, 2, '.', '');
    }

    public function total_egresos_caja_virtual($id_caja)
    {
        $idsede = session('key')->sede_id;
        $envio = $this->tipo_envio_sunat();

        $total = DB::table('movimientos')
            ->join('conceptos', 'movimientos.concepto_id', '=', 'conceptos.id')
            ->where('movimientos.estado', '=', 1)
            ->where('conceptos.tipo_movimiento', '=', 'EGRESO')
            ->where('movimientos.sede_id', '=', $idsede)
            ->where('movimientos.tipo_envio', '=', $envio)
            ->where('movimientos.tipo_caja', '=', 'VIRTUAL')
            ->where('movimientos.id_sesion_caja', '=', $id_caja)
            ->sum('movimientos.monto');
        return number_format($total, 2, '.', '');
    }

    public function total_caja_virtual($id_caja)
    {
        $total = $this->total_ingresos_caja_virtual($id_caja) - $this->total_egresos_caja_virtual($id_caja);

        return number_format($total, 2, '.', '');
    }

    public function generar_movimiento($tipo_movimiento, $forma_pago, $concepto, $monto, $descripcion, $comprobante, $moneda, $des_comprobante, $estado)
    {
        //DB::beginTransaction();

        //try{

        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $envio = $this->tipo_envio_sunat();

        $caja = Caja::where('user_id', '=', $user_id)->where('tipo_envio', '=', $envio)->where('sede_id', '=', $idsede)->orderBy('id', 'desc')->limit(1)->first();

        $id_caja = $caja->id;

        // Log de valores iniciales
        Log::info('Generar movimiento - valores iniciales', [
            'tipo_movimiento' => $tipo_movimiento,
            'forma_pago' => $forma_pago,
            'concepto' => $concepto,
            'monto' => $monto,
            'descripcion' => $descripcion,
            'comprobante' => $comprobante,
            'moneda' => $moneda,
            'des_comprobante' => $des_comprobante,
            'estado' => $estado,
            'idsede' => $idsede,
            'user_id' => $user_id,
            'id_caja' => $id_caja,
            'tipo_envio' => $envio,
        ]);

        if ($forma_pago == 1) {
            $tipo_caja = 'FISICA';
            $total = $this->total_caja_fisica($id_caja);
        } else {
            $tipo_caja = 'VIRTUAL';
            $total = $this->total_caja_virtual($id_caja);
        }


        if ($tipo_movimiento == "EGRESO") {
            if ($monto > $total) {
                return 0;
            }
        }

        $mov = new Movimientos;

        $mov->id_sesion_caja = $id_caja;
        $mov->forma_pago_id = $forma_pago;
        $mov->concepto_id = $concepto;
        $mov->fecha = date('Y-m-d');
        $mov->hora = date('H:i:s');
        $mov->monto = $monto;
        $mov->descripcion = $descripcion;
        $mov->tipo_comprobante_id = $comprobante;
        $mov->moneda_id = $moneda;
        $mov->descripcion_comprobante = $des_comprobante;
        $mov->tipo_envio = $envio;
        $mov->sede_id = $idsede;
        $mov->tipo_caja = $tipo_caja;
        $mov->estado = $estado;

        $mov->save();

        //$query_id = Movimientos::orderBy('id', 'asc')->get();
        //$mov_id = $query_id->last();

        //DB::commit();

        //return $mov_id['id'];
        return $mov->id;

        //}catch (Exception $e) {

        //return  response()->json($e);

        //}

    }

    public function correlativos($comprobante)
    {
        //DB::beginTransaction();

        //try{
        $idsede = session('key')->sede_id;
        $user_id = session('key')->id;

        $tipo_envio = new FuncionesController;

        $envio = $tipo_envio->tipo_envio_sunat();

        $correlativo = Correlativos::where('sede_id', '=', $idsede)->where('tipo_comprobante_id', '=', $comprobante)->where('tipo_envio', '=', $envio)->first();

        $nuevo_correlativo = $correlativo['correlativo'] + 1;

        $numero = Correlativos::find($correlativo['id']);

        $numero->correlativo = $nuevo_correlativo;

        $numero->save();
        //DB::commit();
        return $correlativo;
        //}catch (Exception $e) {

        // return  response()->json($e);

        //}
    }

    public function aumentar_descontar_stock($tipo, $idubicacion, $idproducto, $cantidad, $tipo_envio)
    {
        //DB::beginTransaction();

        // try{

        $data_almacen = Detalle_almacen_productos::where('ubicacion_id', '=', $idubicacion)->where('producto_id', '=', $idproducto)->where('tipo_envio', '=', $tipo_envio)->first();

        if ($tipo == 0) {
            $stock = $data_almacen['stock'] - $cantidad;
        } else {
            $stock = $data_almacen['stock'] + $cantidad;
        }

        $data_almacen->stock = $stock;

        $data_almacen->save();

        //DB::commit();

        return $stock;
        // }catch (Exception $e) {

        //return  response()->json($e);

        //}


    }

    public function ubicacion_almacen_interno($almacen_id, $tipo)
    {



        $almacen = DB::table('almacenes as a')
            ->join('stock_location as s', 's.almacen_id', '=', 'a.id')
            ->select('s.id')
            ->where('s.almacen_id', '=', $almacen_id)
            ->where('s.name', '=', $tipo)
            //->where('s.tipo_envio','=',$tipopro)
            ->first();

        return $almacen->id;
    }
}
