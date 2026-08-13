<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminApiController extends Controller
{
    /**
     * GET /api/admin/dashboard?sede_id=&fecha=
     * Resumen agregado de la fecha consultada (por defecto hoy): ventas y cobranzas
     * de todas las sedes/vendedores, con desglose por sede. Incluye la lista de
     * sedes para el filtro del cliente.
     */
    public function dashboard(Request $request)
    {
        $fecha = $request->input('fecha', date('Y-m-d'));

        $ventasQuery = DB::table('ventas')
            ->where('fecha', $fecha)
            ->whereNull('fecha_eliminacion');
        $cobrosQuery = DB::table('recibos')
            ->where('fech_rec', $fecha)
            ->where('esta_rec', '!=', 'ANULADO');
        $inicialQuery = DB::table('venta_formapago as vf')
            ->join('ventas as v', 'v.id', '=', 'vf.venta_id')
            ->where('v.fecha', $fecha)
            ->whereNull('v.fecha_eliminacion')
            ->where('v.tipo_pago_id', 2);

        if ($request->filled('sede_id')) {
            $ventasQuery->where('sede_id', $request->sede_id);
            $cobrosQuery->where('sede_id', $request->sede_id);
            $inicialQuery->where('v.sede_id', $request->sede_id);
        }

        $ventasContadoQuery = (clone $ventasQuery)->where('tipo_pago_id', '!=', 2);
        $totalVentasContado = (float) $ventasContadoQuery->sum('monto');
        $cantVentasContado  = (int) $ventasContadoQuery->count();

        $ventasCreditoQuery = (clone $ventasQuery)->where('tipo_pago_id', 2);
        $totalVentasCredito = (float) $ventasCreditoQuery->sum('monto');
        $cantVentasCredito  = (int) $ventasCreditoQuery->count();

        $totalInicial = (float) (clone $inicialQuery)->sum('vf.monto');
        $cantInicial  = (int) (clone $inicialQuery)->count();

        $totalCobranzas = (float) (clone $cobrosQuery)->sum('mont_rec');
        $cantCobranzas  = (int) (clone $cobrosQuery)->count();

        $ventasPorSede = DB::table('ventas')
            ->join('sedes as s', 'ventas.sede_id', '=', 's.id')
            ->where('ventas.fecha', $fecha)
            ->whereNull('ventas.fecha_eliminacion')
            ->select(
                's.id as sede_id',
                's.nombre as sede_nombre',
                DB::raw('COALESCE(SUM(ventas.monto),0) as total_ventas'),
                DB::raw('COUNT(ventas.id) as cantidad_ventas')
            )
            ->groupBy('s.id', 's.nombre')
            ->get()
            ->keyBy('sede_id');

        $cobrosPorSede = DB::table('recibos')
            ->join('sedes as s', 'recibos.sede_id', '=', 's.id')
            ->where('recibos.fech_rec', $fecha)
            ->where('recibos.esta_rec', '!=', 'ANULADO')
            ->select(
                's.id as sede_id',
                DB::raw('COALESCE(SUM(recibos.mont_rec),0) as total_cobros'),
                DB::raw('COUNT(recibos.id) as cantidad_cobros')
            )
            ->groupBy('s.id')
            ->get()
            ->keyBy('sede_id');

        $sedes = Sede::where('estado', '1')
            ->where('nombre', 'not ilike', 'TODOS')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        $porSede = $sedes->map(function ($s) use ($ventasPorSede, $cobrosPorSede) {
            $v = $ventasPorSede->get($s->id);
            $c = $cobrosPorSede->get($s->id);
            return [
                'sede_id'         => $s->id,
                'sede_nombre'     => $s->nombre,
                'ventas_total'    => (float) ($v->total_ventas ?? 0),
                'ventas_cantidad' => (int) ($v->cantidad_ventas ?? 0),
                'cobros_total'    => (float) ($c->total_cobros ?? 0),
                'cobros_cantidad' => (int) ($c->cantidad_cobros ?? 0),
            ];
        })->values();

        return response()->json([
            'status'    => true,
            'fecha'     => $fecha,
            'ventas'    => [
                'contado' => ['total' => round($totalVentasContado, 2), 'cantidad' => $cantVentasContado],
                'credito' => ['total' => round($totalVentasCredito, 2), 'cantidad' => $cantVentasCredito],
                'inicial_credito' => ['total' => round($totalInicial, 2), 'cantidad' => $cantInicial],
            ],
            'cobranzas' => ['total' => round($totalCobranzas, 2), 'cantidad' => $cantCobranzas],
            'sedes'     => $sedes,
            'por_sede'  => $porSede,
        ]);
    }

    /**
     * GET /api/admin/ventas?fecha_desde&fecha_hasta&sede_id&buscar
     * Ventas de todas las sedes/vendedores (sin restricción por vendedor logueado).
     */
    public function ventas(Request $request)
    {
        $query = DB::table('ventas as v')
            ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            ->join('sedes as s', 'v.sede_id', '=', 's.id')
            ->leftJoin('vendedores as vd', 'v.vendedor_id', '=', 'vd.id')
            ->leftJoin('tipo_pagos as tp', 'v.tipo_pago_id', '=', 'tp.id')
            ->select(
                'v.id', 'v.fecha', 'v.hora',
                'v.serie_comprobante', 'v.numero_comprobante',
                'v.monto', 'v.tipo_pago_id', 'v.venta_estado',
                'v.estado_liquidacion', 'v.estado_nota', 'v.fecha_eliminacion',
                'v.sede_id', 's.nombre as sede_nombre',
                'v.vendedor_id', 'vd.nombre as vendedor_nombre',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as cliente_nombre"),
                'c.documento as cliente_documento',
                'tp.descripcion as tipo_pago'
            );

        if ($request->filled('sede_id')) {
            $query->where('v.sede_id', $request->sede_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->where('v.fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('v.fecha', '<=', $request->fecha_hasta);
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('c.razon_social', 'ilike', "%$buscar%")
                  ->orWhere('c.nomb_per', 'ilike', "%$buscar%")
                  ->orWhere('c.pate_per', 'ilike', "%$buscar%")
                  ->orWhere('c.mate_per', 'ilike', "%$buscar%")
                  ->orWhere('c.documento', 'like', "%$buscar%")
                  ->orWhere('vd.nombre', 'ilike', "%$buscar%")
                  ->orWhere('v.serie_comprobante', 'like', "%$buscar%")
                  ->orWhere('v.numero_comprobante', 'like', "%$buscar%");
            });
        }

        $items = $query->orderBy('v.id', 'desc')->limit(200)->get()->map(function ($v) {
            $estado = 'ACTIVA';
            if ($v->fecha_eliminacion) {
                $estado = 'ANULADA';
            } elseif ($v->estado_nota) {
                $estado = 'CON NOTA';
            } elseif ($v->venta_estado === '0' || $v->venta_estado === 0) {
                $estado = 'PENDIENTE';
            } elseif ($v->estado_liquidacion === 'PENDIENTE') {
                $estado = 'POR LIQUIDAR';
            }
            return [
                'id'                 => (int) $v->id,
                'fecha'              => $v->fecha,
                'hora'               => $v->hora,
                'comprobante'        => trim(($v->serie_comprobante ?? '') . '-' . ($v->numero_comprobante ?? ''), '-'),
                'monto'              => (float) $v->monto,
                'tipo_pago_id'       => (int) $v->tipo_pago_id,
                'tipo_pago'          => $v->tipo_pago,
                'sede_id'            => (int) $v->sede_id,
                'sede_nombre'        => $v->sede_nombre,
                'vendedor_id'        => $v->vendedor_id !== null ? (int) $v->vendedor_id : null,
                'vendedor_nombre'    => $v->vendedor_nombre,
                'cliente_nombre'     => $v->cliente_nombre,
                'cliente_documento'  => $v->cliente_documento,
                'estado'             => $estado,
                'estado_liquidacion' => $v->estado_liquidacion,
            ];
        })->values();

        return response()->json(['status' => true, 'items' => $items, 'total' => $items->count()]);
    }

    /**
     * GET /api/admin/ventas/{id}/detalle
     */
    public function ventaDetalle($id)
    {
        $venta = DB::table('ventas as v')
            ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            ->join('sedes as s', 'v.sede_id', '=', 's.id')
            ->leftJoin('vendedores as vd', 'v.vendedor_id', '=', 'vd.id')
            ->leftJoin('tipo_comprobantes as tc', 'v.tipo_comprobante_id', '=', 'tc.id')
            ->leftJoin('tipo_pagos as tp', 'v.tipo_pago_id', '=', 'tp.id')
            ->where('v.id', $id)
            ->select(
                'v.id', 'v.fecha', 'v.hora',
                'v.serie_comprobante', 'v.numero_comprobante',
                'v.monto', 'v.descuento', 'v.tipo_pago_id',
                'v.venta_estado', 'v.estado_liquidacion', 'v.estado_nota', 'v.fecha_eliminacion',
                'v.sede_id', 's.nombre as sede_nombre',
                'v.vendedor_id', 'vd.nombre as vendedor_nombre',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as cliente_nombre"),
                'c.documento as cliente_documento',
                'c.dire_per as cliente_direccion',
                'tc.descripcion as tipo_comprobante',
                'tp.descripcion as tipo_pago'
            )
            ->first();

        if (!$venta) {
            return response()->json(['status' => false, 'message' => 'Venta no encontrada.'], 404);
        }

        $productos = DB::table('detalle_venta as dv')
            ->join('productos as p', 'p.id', '=', 'dv.producto_id')
            ->where('dv.venta_id', $id)
            ->select('p.id', 'p.nomb_pro as nombre', 'dv.cantidad', 'dv.precio', 'dv.subtotal')
            ->orderBy('p.nomb_pro')
            ->get()
            ->map(fn ($p) => [
                'id'       => (int) $p->id,
                'nombre'   => $p->nombre,
                'cantidad' => (float) $p->cantidad,
                'precio'   => (float) $p->precio,
                'subtotal' => (float) $p->subtotal,
            ])
            ->values();

        return response()->json([
            'status' => true,
            'venta'  => [
                'id'                 => (int) $venta->id,
                'fecha'              => $venta->fecha,
                'hora'               => $venta->hora,
                'comprobante'        => trim(($venta->serie_comprobante ?? '') . '-' . ($venta->numero_comprobante ?? ''), '-'),
                'serie'              => $venta->serie_comprobante,
                'numero'             => $venta->numero_comprobante,
                'monto'              => (float) $venta->monto,
                'descuento'          => (float) $venta->descuento,
                'tipo_pago_id'       => (int) $venta->tipo_pago_id,
                'tipo_pago'          => $venta->tipo_pago,
                'tipo_comprobante'   => $venta->tipo_comprobante,
                'sede_id'            => (int) $venta->sede_id,
                'sede_nombre'        => $venta->sede_nombre,
                'vendedor_id'        => $venta->vendedor_id !== null ? (int) $venta->vendedor_id : null,
                'vendedor_nombre'    => $venta->vendedor_nombre,
                'cliente_nombre'     => $venta->cliente_nombre,
                'cliente_documento'  => $venta->cliente_documento,
                'cliente_direccion'  => $venta->cliente_direccion,
                'estado_liquidacion' => $venta->estado_liquidacion,
            ],
            'productos'      => $productos,
            'total_unidades' => $productos->sum('cantidad'),
        ]);
    }

    /**
     * GET /api/admin/cobros?fecha_desde&fecha_hasta&sede_id&buscar
     * Cobranzas (recibos) de todas las sedes/vendedores.
     */
    public function cobros(Request $request)
    {
        $query = DB::table('recibos as r')
            ->join('clientes as c', 'r.cliente_id', '=', 'c.id')
            ->join('sedes as s', 'r.sede_id', '=', 's.id')
            ->leftJoin('vendedores as vd', 'r.vendedor_id', '=', 'vd.id')
            ->leftJoin('movimientos as m', 'm.id', '=', 'r.id_movimiento')
            ->leftJoin('forma_pagos as fp', 'fp.id', '=', 'm.forma_pago_id')
            ->select(
                'r.id', 'r.fech_rec', 'r.num_recibo', 'r.mont_rec',
                'r.esta_rec', 'r.estado_liquidacion', 'r.created_at',
                'r.sede_id', 's.nombre as sede_nombre',
                'r.vendedor_id', 'vd.nombre as vendedor_nombre',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as cliente_nombre"),
                'c.documento as cliente_documento',
                'fp.descripcion as forma_pago'
            );

        if ($request->filled('sede_id')) {
            $query->where('r.sede_id', $request->sede_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->where('r.fech_rec', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('r.fech_rec', '<=', $request->fecha_hasta);
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('c.razon_social', 'ilike', "%$buscar%")
                  ->orWhere('c.nomb_per', 'ilike', "%$buscar%")
                  ->orWhere('c.pate_per', 'ilike', "%$buscar%")
                  ->orWhere('c.mate_per', 'ilike', "%$buscar%")
                  ->orWhere('c.documento', 'like', "%$buscar%")
                  ->orWhere('vd.nombre', 'ilike', "%$buscar%")
                  ->orWhere('r.num_recibo', 'like', "%$buscar%");
            });
        }

        $items = $query->orderBy('r.id', 'desc')->limit(200)->get()->map(function ($r) {
            return [
                'id'                 => (int) $r->id,
                'fecha'              => $r->fech_rec,
                'num_recibo'         => $r->num_recibo,
                'monto'              => (float) $r->mont_rec,
                'estado'             => $r->esta_rec,
                'estado_legible'     => $r->esta_rec == 'ANULADO' ? 'ANULADO' : 'EMITIDO',
                'estado_liquidacion' => $r->estado_liquidacion,
                'sede_id'            => (int) $r->sede_id,
                'sede_nombre'        => $r->sede_nombre,
                'vendedor_id'        => $r->vendedor_id !== null ? (int) $r->vendedor_id : null,
                'vendedor_nombre'    => $r->vendedor_nombre,
                'cliente_nombre'     => $r->cliente_nombre,
                'cliente_documento'  => $r->cliente_documento,
                'forma_pago'         => $r->forma_pago,
                'created_at'         => $r->created_at,
            ];
        })->values();

        return response()->json(['status' => true, 'items' => $items, 'total' => $items->count()]);
    }

    /**
     * GET /api/admin/cobros/{id}/detalle
     */
    public function cobroDetalle($id)
    {
        $recibo = DB::table('recibos as r')
            ->join('clientes as c', 'r.cliente_id', '=', 'c.id')
            ->join('sedes as s', 'r.sede_id', '=', 's.id')
            ->leftJoin('vendedores as vd', 'r.vendedor_id', '=', 'vd.id')
            ->leftJoin('movimientos as m', 'm.id', '=', 'r.id_movimiento')
            ->leftJoin('forma_pagos as fp', 'fp.id', '=', 'm.forma_pago_id')
            ->where('r.id', $id)
            ->select(
                'r.id', 'r.fech_rec', 'r.num_recibo', 'r.mont_rec',
                'r.esta_rec', 'r.estado_liquidacion', 'r.obse_rec',
                'r.created_at', 'r.sede_id', 's.nombre as sede_nombre',
                'r.vendedor_id', 'vd.nombre as vendedor_nombre',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as cliente_nombre"),
                'c.documento as cliente_documento',
                'c.dire_per as cliente_direccion',
                'fp.descripcion as forma_pago'
            )
            ->first();

        if (!$recibo) {
            return response()->json(['status' => false, 'message' => 'Recibo no encontrado.'], 404);
        }

        $amortizaciones = DB::table('amortizaciones as a')
            ->join('cuotas as cu', 'a.cuota_id', '=', 'cu.id')
            ->join('creditos as cr', 'cu.credito_id', '=', 'cr.id')
            ->leftJoin('ventas as v', 'cr.id_venta', '=', 'v.id')
            ->leftJoin('tipo_comprobantes as tc', 'v.tipo_comprobante_id', '=', 'tc.id')
            ->where('a.recibo_id', $id)
            ->select(
                'a.id', 'a.mont_amo', 'a.capi_amo', 'a.inte_amo',
                'a.saldo_cuo as saldo_restante_cuota',
                'cu.numero_cuo', 'cu.mont_cuo', 'cu.fven_cuo',
                'cr.id as credito_id', 'cr.impo_cre as total_credito',
                'v.serie_comprobante', 'v.numero_comprobante',
                'tc.descripcion as tipo_comprobante'
            )
            ->get()
            ->map(fn ($a) => [
                'id'                   => (int) $a->id,
                'monto_amortizado'     => (float) $a->mont_amo,
                'capital'              => (float) $a->capi_amo,
                'interes'              => (float) $a->inte_amo,
                'saldo_restante_cuota' => (float) $a->saldo_restante_cuota,
                'numero_cuota'         => (int) $a->numero_cuo,
                'monto_cuota'          => (float) $a->mont_cuo,
                'vencimiento_cuota'    => $a->fven_cuo,
                'credito_id'           => (int) $a->credito_id,
                'total_credito'        => (float) $a->total_credito,
                'comprobante'          => trim(($a->serie_comprobante ?? '') . '-' . ($a->numero_comprobante ?? ''), '-'),
                'tipo_comprobante'     => $a->tipo_comprobante,
            ])
            ->values();

        return response()->json([
            'status' => true,
            'recibo' => [
                'id'                 => (int) $recibo->id,
                'fecha'              => $recibo->fech_rec,
                'num_recibo'         => $recibo->num_recibo,
                'monto'              => (float) $recibo->mont_rec,
                'estado'             => $recibo->esta_rec,
                'estado_legible'     => $recibo->esta_rec == 'ANULADO' ? 'ANULADO' : 'EMITIDO',
                'estado_liquidacion' => $recibo->estado_liquidacion,
                'observacion'        => $recibo->obse_rec,
                'sede_id'            => (int) $recibo->sede_id,
                'sede_nombre'        => $recibo->sede_nombre,
                'vendedor_id'        => $recibo->vendedor_id !== null ? (int) $recibo->vendedor_id : null,
                'vendedor_nombre'    => $recibo->vendedor_nombre,
                'cliente_nombre'     => $recibo->cliente_nombre,
                'cliente_documento'  => $recibo->cliente_documento,
                'cliente_direccion'  => $recibo->cliente_direccion,
                'forma_pago'         => $recibo->forma_pago,
            ],
            'amortizaciones'   => $amortizaciones,
            'total_amortizado' => $recibo->mont_rec,
        ]);
    }
}
