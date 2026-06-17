<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Sede;
use App\Permisos;
use App\Http\Controllers\servicios\FuncionesController;

class ReporteKpiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra la vista principal del reporte.
     */
    public function index()
    {
        if (!Permisos::hasPermission('reportes/kpi-creditos', 1)) {
            abort(403, 'No tiene permiso para acceder a este reporte.');
        }

        $user = auth()->user();
        $isAdmin = $user->hasRole('ADMINISTRADOR');
        $idsede = session('key') ? session('key')->sede_id : null;

        // Tipo de envío de la sede del usuario logueado
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        if ($isAdmin) {
            // Admin: ver todas las sedes activas del MISMO tipo_envio
            $sedes = Sede::where('estado', '=', '1')
                ->where('id', '!=', 1) // Excluir "TODOS"
                ->where('tipo_envio', '=', $envio)
                ->orderBy('nombre', 'asc')
                ->get();
        } else {
            // No admin: solo su propia sede
            $sedes = Sede::where('id', $idsede)->get();
        }

        return view('reportes.kpi_creditos', compact('sedes', 'isAdmin'));
    }

    /**
     * Endpoint AJAX que retorna la data del KPI y la lista de créditos.
     */
    public function getData(Request $request)
    {
        if (!Permisos::hasPermission('reportes/kpi-creditos', 1)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = auth()->user();
        $isAdmin = $user->hasRole('ADMINISTRADOR');
        $idsede = session('key') ? session('key')->sede_id : null;

        // Filtro por tipo_envio (mismo que la sede del usuario)
        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        // Filtro opcional por sede desde el select
        $sedeFilter = $request->get('sede_id', 'all');

        $query = DB::table('creditos as c')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            ->join('sedes as s', 'c.sede_id', '=', 's.id')
            ->leftJoin('cuotas as cu', function($join) {
                $join->on('cu.credito_id', '=', 'c.id')
                     ->where('cu.esta_cuo', '=', 'PENDIENTE');
            })
            ->select(
                'c.id as credito_id',
                'c.impo_cre',
                'c.fech_cre',
                'c.sede_id',
                's.nombre as sede_nombre',
                'cl.razon_social as cliente_nombre',
                'cl.documento as cliente_documento',
                DB::raw('COALESCE(SUM(cu.saldo_cuo), 0) as saldo_pendiente'),
                DB::raw('COALESCE(MAX(GREATEST(CURRENT_DATE - cu.fven_cuo, 0)), 0) as max_dias_atraso')
            )
            ->where('c.esta_cre', '=', '1')
            ->where('c.sede_id', '!=', 1)
            ->where('s.tipo_envio', '=', $envio);

        if (!$isAdmin) {
            // No admin: siempre su propia sede
            $query->where('c.sede_id', '=', $idsede);
        } elseif ($sedeFilter !== 'all' && $sedeFilter !== null && $sedeFilter !== '') {
            // Admin con sede específica seleccionada
            $query->where('c.sede_id', '=', $sedeFilter);
        }
        // Admin sin selección o con "all" -> ve todas las sedes del tipo_envio actual

        $creditsRaw = $query->groupBy('c.id', 'c.impo_cre', 'c.fech_cre', 'c.sede_id', 's.nombre', 'cl.razon_social', 'cl.documento')
            ->get();

        $credits = [];
        
        $summary = [
            'total_creditos' => 0,
            'total_saldo' => 0,
            'normal' => ['cantidad' => 0, 'saldo' => 0],
            'potencial' => ['cantidad' => 0, 'saldo' => 0],
            'deficiente' => ['cantidad' => 0, 'saldo' => 0],
            'dudoso' => ['cantidad' => 0, 'saldo' => 0],
            'perdida' => ['cantidad' => 0, 'saldo' => 0]
        ];

        $sedesData = [];

        foreach ($creditsRaw as $c) {
            $max_dias = (int)$c->max_dias_atraso;
            $saldo = (float)$c->saldo_pendiente;
            
            if ($max_dias <= 8) {
                $cat_key = 'normal';
                $categoria = 'Normal';
                $color = 'success';
                $color_hex = '#2ec4b6';
            } elseif ($max_dias <= 30) {
                $cat_key = 'potencial';
                $categoria = 'Problemas Potenciales';
                $color = 'warning';
                $color_hex = '#ffbf00';
            } elseif ($max_dias <= 60) {
                $cat_key = 'deficiente';
                $categoria = 'Deficiente';
                $color = 'orange';
                $color_hex = '#ff7f50';
            } elseif ($max_dias <= 120) {
                $cat_key = 'dudoso';
                $categoria = 'Dudoso';
                $color = 'danger';
                $color_hex = '#e71d36';
            } else {
                $cat_key = 'perdida';
                $categoria = 'Pérdida';
                $color = 'dark';
                $color_hex = '#343a40';
            }

            $creditoObj = [
                'credito_id' => $c->credito_id,
                'impo_cre' => (float)$c->impo_cre,
                'fech_cre' => date('d/m/Y', strtotime($c->fech_cre)),
                'sede_id' => $c->sede_id,
                'sede_nombre' => $c->sede_nombre,
                'cliente_nombre' => $c->cliente_nombre,
                'cliente_documento' => $c->cliente_documento,
                'saldo_pendiente' => $saldo,
                'max_dias_atraso' => $max_dias,
                'categoria' => $categoria,
                'cat_key' => $cat_key,
                'color' => $color,
                'color_hex' => $color_hex
            ];

            $credits[] = $creditoObj;

            $summary['total_creditos']++;
            $summary['total_saldo'] += $saldo;
            $summary[$cat_key]['cantidad']++;
            $summary[$cat_key]['saldo'] += $saldo;

            $sId = $c->sede_id;
            if (!isset($sedesData[$sId])) {
                $sedesData[$sId] = [
                    'sede_id' => $sId,
                    'sede_nombre' => $c->sede_nombre,
                    'total_creditos' => 0,
                    'total_saldo' => 0,
                    'normal' => ['cantidad' => 0, 'saldo' => 0],
                    'potencial' => ['cantidad' => 0, 'saldo' => 0],
                    'deficiente' => ['cantidad' => 0, 'saldo' => 0],
                    'dudoso' => ['cantidad' => 0, 'saldo' => 0],
                    'perdida' => ['cantidad' => 0, 'saldo' => 0]
                ];
            }

            $sedesData[$sId]['total_creditos']++;
            $sedesData[$sId]['total_saldo'] += $saldo;
            $sedesData[$sId][$cat_key]['cantidad']++;
            $sedesData[$sId][$cat_key]['saldo'] += $saldo;
        }

        $sedesSummary = array_values($sedesData);

        usort($sedesSummary, function($a, $b) {
            return strcmp($a['sede_nombre'], $b['sede_nombre']);
        });

        return response()->json([
            'summary' => $summary,
            'sedes_summary' => $sedesSummary,
            'credits' => $credits
        ]);
    }
}
