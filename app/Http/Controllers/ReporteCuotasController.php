<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;

class ReporteCuotasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $hoy = date('Y-m-d');
        $idsede = session('key')->sede_id;

        $buscar = $request->get('buscar', '');
        $sectoresSeleccionados = $request->get('sectores', []);

        $sectores = DB::table('sectores')->where('estado', '=', 'ACTIVO')->get();

        $query = DB::table('cuotas as cu')
            ->join('creditos as c', 'cu.credito_id', '=', 'c.id')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            ->leftJoin('amortizaciones as am', 'am.cuota_id', '=', 'cu.id')
            ->leftJoin('recibos as r', 'r.id', '=', 'am.recibo_id')
            ->leftJoin('movimientos as m', 'm.id', '=', 'r.id_movimiento')
            ->leftJoin('forma_pagos as fp', 'fp.id', '=', 'm.forma_pago_id')
            ->leftJoin('sectores as sc', 'cl.id_sector', '=', 'sc.id')
            ->select(
                'cl.id as cliente_id',
                'cl.razon_social',
                'cl.pate_per',
                'cl.mate_per',
                'cl.razon_social',
                'cl.documento',
                'cl.dire_per',
                'sc.nomb_sec as sector_cliente',
                'c.id as credito_id',
                'c.id_venta',
                'fp.descripcion as metodo_pago',
                'cu.numero_cuo',
                'cu.mont_cuo',
                'cu.saldo_cuo',
                'cu.fven_cuo as fecha_amortizacion'
            )
            ->where('c.esta_cre', '=', '1')
            ->where('cu.esta_cuo', '=', 'PENDIENTE')
            ->where('cu.fven_cuo', '<', $hoy)
            ->where('c.sede_id', '=', $idsede);

        if (!empty($buscar)) {
            $query->where(function($q) use ($buscar) {
                $q->where('cl.razon_social', 'like', '%' . $buscar . '%')
                  ->orWhere('cl.documento', 'like', '%' . $buscar . '%');
            });
        }

        if (!empty($sectoresSeleccionados)) {
            $query->whereIn('cl.id_sector', $sectoresSeleccionados);
        }

        $cuotasVencidas = $query->orderBy('cl.razon_social', 'asc')
            ->orderBy('c.id', 'asc')
            ->orderBy('cu.numero_cuo', 'asc')
            ->get();

        $datos = [];

        foreach ($cuotasVencidas as $cuota) {

            // Obtener productos
            $productos = DB::table('detalle_venta as dv')
                ->join('productos as p', 'dv.producto_id', '=', 'p.id')
                ->where('dv.venta_id', '=', $cuota->id_venta)
                ->select('p.nomb_pro')
                ->pluck('nomb_pro')
                ->toArray();

            $nombres_productos = implode(', ', $productos);

            $datos[] = [
                'credito_id' => $cuota->credito_id,
                'documento' => $cuota->documento,
                'cliente' => $cuota->razon_social,
                'direccion' => $cuota->dire_per,
                'sector' => $cuota->sector_cliente,
                'productos' => $nombres_productos,
                'numero_cuo' => $cuota->numero_cuo,
                'mont_cuo' => $cuota->mont_cuo,
                'saldo_cuo' => $cuota->saldo_cuo,
                'fecha_amortizacion' => $cuota->fecha_amortizacion,
                'metodo_pago' => $cuota->metodo_pago
            ];
        }

        return view('reportecuotas.index', compact('datos', 'hoy', 'sectores', 'buscar', 'sectoresSeleccionados'));
    }
}
