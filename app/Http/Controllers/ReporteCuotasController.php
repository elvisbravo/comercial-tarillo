<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ReporteCuotasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Solo retorna la vista con los sectores para el filtro.
     * Los datos se cargan via AJAX con getData().
     */
    public function index(Request $request)
    {
        $sectores = DB::table('sectores')->where('estado', '=', 'ACTIVO')->get();

        return view('reportecuotas.index', compact('sectores'));
    }

    /**
     * Endpoint AJAX: devuelve los creditos con cuotas vencidas en JSON.
     */
    public function getData(Request $request)
    {
        $hoy = date('Y-m-d');
        $idsede = session('key')->sede_id;

        $buscar               = $request->get('buscar', '');
        $sectoresSeleccionados = $request->get('sectores', []);

        // Creditos con al menos una cuota PENDIENTE y vencida, agrupados por credito
        $query = DB::table('creditos as c')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            ->leftJoin('sectores as sc', 'cl.id_sector', '=', 'sc.id')
            ->join('cuotas as cu_venc', function ($j) use ($hoy) {
                $j->on('cu_venc.credito_id', '=', 'c.id')
                  ->where('cu_venc.esta_cuo', '=', 'PENDIENTE')
                  ->where('cu_venc.fven_cuo', '<', $hoy);
            })
            ->select(
                'c.id as credito_id',
                'cl.razon_social',
                'cl.dire_per',
                'cl.telefono',
                'sc.nomb_sec as sector',
                DB::raw('SUM(cu_venc.mont_cuo) as monto_total'),
                DB::raw('SUM(cu_venc.saldo_cuo) as saldo_total'),
                DB::raw('MIN(cu_venc.numero_cuo) as primera_cuota_vencida'),
                DB::raw('MIN(cu_venc.fven_cuo) as proxima_fecha')
            )
            ->where('c.esta_cre', '=', '1')
            ->where('c.sede_id', '=', $idsede)
            ->groupBy('c.id', 'cl.razon_social', 'cl.dire_per', 'cl.telefono', 'sc.nomb_sec');

        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('cl.razon_social', 'like', '%' . $buscar . '%')
                  ->orWhere('cl.documento', 'like', '%' . $buscar . '%');
            });
        }

        if (!empty($sectoresSeleccionados)) {
            $query->whereIn('cl.id_sector', $sectoresSeleccionados);
        }

        $creditos = $query->orderBy('cl.razon_social', 'asc')->get();

        $datos = [];
        foreach ($creditos as $cred) {
            $total_cuotas = DB::table('cuotas')
                ->where('credito_id', $cred->credito_id)
                ->count();

            $cuotas_pagadas = DB::table('cuotas')
                ->where('credito_id', $cred->credito_id)
                ->where('esta_cuo', 'PAGADO')
                ->count();

            $ultima_pago = DB::table('amortizaciones as am')
                ->join('cuotas as cu', 'am.cuota_id', '=', 'cu.id')
                ->where('cu.credito_id', $cred->credito_id)
                ->max('am.created_at');

            $datos[] = [
                'credito_id'     => $cred->credito_id,
                'cliente'        => $cred->razon_social,
                'direccion'      => $cred->dire_per,
                'sector'         => $cred->sector ?? 'S/N',
                'telefono'       => $cred->telefono ?? '-',
                'monto_total'    => $cred->monto_total,
                'saldo_total'    => $cred->saldo_total,
                'cuotas_pagadas' => $cuotas_pagadas,
                'total_cuotas'   => $total_cuotas,
                'proxima_fecha'  => $cred->proxima_fecha,
                'ultima_pago'    => $ultima_pago ? date('d/m/Y', strtotime($ultima_pago)) : '-',
            ];
        }

        return response()->json([
            'datos'       => $datos,
            'total_saldo' => array_sum(array_column($datos, 'saldo_total')),
            'total_monto' => array_sum(array_column($datos, 'monto_total')),
            'count'       => count($datos),
        ]);
    }
}
