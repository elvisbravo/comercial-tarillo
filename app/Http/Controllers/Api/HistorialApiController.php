<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\servicios\FuncionesController;
use App\Sede;
use App\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistorialApiController extends Controller
{
    /**
     * GET /api/vendedor/historial-cargas?fecha_desde&fecha_hasta&buscar
     */
    public function cargas(Request $request)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $idsede = $user->sede_id;
        $envio = $this->tipoEnvio($idsede);

        // Some traslados have cliente_id = users.id, others have cliente_id = vendedores.id (legacy)
        $vendedor = Vendedor::where('usuario_id', $user->id)->first();
        $clienteIds = [$user->id];
        if ($vendedor) {
            $clienteIds[] = $vendedor->id;
        }

        $query = DB::table('traslados as t')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->select(
                't.id', 't.fecha', 't.hora', 't.serie', 't.correlativo',
                't.motivo', 't.estado', 't.tipo_envio',
                'u.name as usuario_nombre',
                DB::raw('(SELECT COALESCE(SUM(dt.cantidad), 0) FROM detalle_traslado dt WHERE dt.traslado_id = t.id) as total_unidades')
            )
            ->where('t.serie', 'LIKE', 'CAR%')
            ->whereIn('t.cliente_id', $clienteIds)
            ->where('t.sede_id', $idsede)
            ->where('t.tipo_envio', $envio);

        if ($request->filled('fecha_desde')) {
            $query->where('t.fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('t.fecha', '<=', $request->fecha_hasta);
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('t.correlativo', 'like', "%$buscar%")
                  ->orWhere('t.motivo', 'like', "%$buscar%");
            });
        }

        $items = $query->orderBy('t.id', 'desc')->limit(200)->get()->map(function ($t) {
            return [
                'id'              => (int) $t->id,
                'fecha'           => $t->fecha,
                'hora'            => $t->hora,
                'serie'           => $t->serie,
                'correlativo'     => $t->correlativo,
                'motivo'          => $t->motivo,
                'estado'          => (int) $t->estado,
                'estado_legible'  => $t->estado == 1 ? 'ACTIVO' : 'ANULADO',
                'total_unidades'  => (float) $t->total_unidades,
                'usuario_nombre'  => $t->usuario_nombre,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'items'  => $items,
            'total'  => $items->count(),
        ]);
    }

    /**
     * GET /api/vendedor/historial-cargas/{id}/detalle
     */
    public function cargaDetalle($id)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }
        $idsede = $user->sede_id;
        $envio = $this->tipoEnvio($idsede);

        $vendedor = Vendedor::where('usuario_id', $user->id)->first();
        $clienteIds = [$user->id];
        if ($vendedor) {
            $clienteIds[] = $vendedor->id;
        }

        $traslado = DB::table('traslados as t')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->where('t.id', $id)
            ->where('t.serie', 'LIKE', 'CAR%')
            ->whereIn('t.cliente_id', $clienteIds)
            ->where('t.sede_id', $idsede)
            ->where('t.tipo_envio', $envio)
            ->select(
                't.id', 't.fecha', 't.hora', 't.serie', 't.correlativo',
                't.motivo', 't.estado',
                'u.name as usuario_nombre'
            )
            ->first();

        if (!$traslado) {
            return response()->json(['status' => false, 'message' => 'Carga no encontrada.'], 404);
        }

        $productos = DB::table('detalle_traslado as dt')
            ->join('productos as p', 'p.id', '=', 'dt.producto_id')
            ->leftJoin('precios as pr', 'pr.articulo_id', '=', 'p.id')
            ->where('dt.traslado_id', $id)
            ->select(
                'p.id',
                'p.nomb_pro as nombre',
                'p.codigo_barras',
                'dt.cantidad',
                DB::raw('COALESCE(pr.precio_contado, 0) as precio_unitario'),
                DB::raw('dt.cantidad * COALESCE(pr.precio_contado, 0) as subtotal')
            )
            ->orderBy('p.nomb_pro')
            ->get()
            ->map(fn ($p) => [
                'id'              => (int) $p->id,
                'nombre'          => $p->nombre,
                'codigo_barras'   => $p->codigo_barras,
                'cantidad'        => (float) $p->cantidad,
                'precio_unitario' => (float) $p->precio_unitario,
                'subtotal'        => (float) $p->subtotal,
            ])
            ->values();

        return response()->json([
            'status'         => true,
            'traslado'       => [
                'id'             => (int) $traslado->id,
                'fecha'          => $traslado->fecha,
                'hora'           => $traslado->hora,
                'serie'          => $traslado->serie,
                'correlativo'    => $traslado->correlativo,
                'motivo'         => $traslado->motivo,
                'estado'         => (int) $traslado->estado,
                'estado_legible' => $traslado->estado == 1 ? 'ACTIVO' : 'ANULADO',
                'usuario_nombre' => $traslado->usuario_nombre,
            ],
            'productos'      => $productos,
            'total_unidades' => $productos->sum('cantidad'),
            'total_valor'    => $productos->sum('subtotal'),
        ]);
    }

    /**
     * GET /api/vendedor/historial-ventas?fecha_desde&fecha_hasta&buscar
     */
    public function ventas(Request $request)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }
        $vendedor = $this->resolveVendedor($user);
        $idsede = $user->sede_id;
        $envio = $this->tipoEnvio($idsede);

        $query = DB::table('ventas as v')
            ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            ->leftJoin('tipo_comprobantes as tc', 'v.tipo_comprobante_id', '=', 'tc.id')
            ->leftJoin('tipo_pagos as tp', 'v.tipo_pago_id', '=', 'tp.id')
            ->select(
                'v.id', 'v.fecha', 'v.hora',
                'v.serie_comprobante', 'v.numero_comprobante',
                'v.monto', 'v.tipo_pago_id', 'v.venta_estado',
                'v.estado_liquidacion', 'v.estado_nota', 'v.fecha_eliminacion',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as cliente_nombre"),
                'c.documento as cliente_documento',
                'tc.descripcion as tipo_comprobante',
                'tp.descripcion as tipo_pago',
                DB::raw('(SELECT COALESCE(SUM(dv.cantidad), 0) FROM detalle_venta dv WHERE dv.venta_id = v.id) as total_unidades')
            )
            ->where('v.vendedor_id', $vendedor->id)
            ->where('v.sede_id', $idsede)
            ->where('v.tipo_envio', $envio);

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
                'id'                  => (int) $v->id,
                'fecha'               => $v->fecha,
                'hora'                => $v->hora,
                'serie'               => $v->serie_comprobante,
                'numero'              => $v->numero_comprobante,
                'comprobante'         => trim(($v->serie_comprobante ?? '') . '-' . ($v->numero_comprobante ?? ''), '-'),
                'monto'               => (float) $v->monto,
                'tipo_pago_id'        => (int) $v->tipo_pago_id,
                'tipo_pago'           => $v->tipo_pago,
                'tipo_comprobante'    => $v->tipo_comprobante,
                'cliente_nombre'      => $v->cliente_nombre,
                'cliente_documento'   => $v->cliente_documento,
                'estado'              => $estado,
                'estado_liquidacion'  => $v->estado_liquidacion,
                'total_unidades'      => (float) $v->total_unidades,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'items'  => $items,
            'total'  => $items->count(),
        ]);
    }

    /**
     * GET /api/vendedor/historial-ventas/{id}/detalle
     */
    public function ventaDetalle($id)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }
        $vendedor = $this->resolveVendedor($user);
        $idsede = $user->sede_id;
        $envio = $this->tipoEnvio($idsede);

        $venta = DB::table('ventas as v')
            ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            ->leftJoin('tipo_comprobantes as tc', 'v.tipo_comprobante_id', '=', 'tc.id')
            ->leftJoin('tipo_pagos as tp', 'v.tipo_pago_id', '=', 'tp.id')
            ->where('v.id', $id)
            ->where('v.vendedor_id', $vendedor->id)
            ->where('v.sede_id', $idsede)
            ->where('v.tipo_envio', $envio)
            ->select(
                'v.id', 'v.fecha', 'v.hora',
                'v.serie_comprobante', 'v.numero_comprobante',
                'v.monto', 'v.descuento', 'v.tipo_pago_id',
                'v.venta_estado', 'v.estado_liquidacion', 'v.estado_nota', 'v.fecha_eliminacion',
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
            'status'   => true,
            'venta'    => [
                'id'                 => (int) $venta->id,
                'fecha'              => $venta->fecha,
                'hora'               => $venta->hora,
                'comprobante'        => trim(($venta->serie_comprobante ?? '') . '-' . ($venta->numero_comprobante ?? ''), '-'),
                'serie'              => $venta->serie_comprobante,
                'numero'             => $venta->numero_comprobante,
                'monto'              => (float) $venta->monto,
                'descuento'          => (float) $venta->descuento,
                'tipo_pago'          => $venta->tipo_pago,
                'tipo_comprobante'   => $venta->tipo_comprobante,
                'cliente_nombre'     => $venta->cliente_nombre,
                'cliente_documento'  => $venta->cliente_documento,
                'cliente_direccion'  => $venta->cliente_direccion,
                'estado_liquidacion' => $venta->estado_liquidacion,
            ],
            'productos' => $productos,
            'total_unidades' => $productos->sum('cantidad'),
        ]);
    }

    /**
     * GET /api/vendedor/historial-cobros?fecha_desde&fecha_hasta&buscar
     */
    public function cobros(Request $request)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }
        $vendedor = $this->resolveVendedor($user);
        $idsede = $user->sede_id;

        $query = DB::table('recibos as r')
            ->join('clientes as c', 'r.cliente_id', '=', 'c.id')
            ->leftJoin('forma_pagos as fp', function ($join) {
                $join->whereRaw('r.fpag_rec = CAST(fp.id AS varchar)');
            })
            ->select(
                'r.id', 'r.fech_rec', 'r.num_recibo', 'r.mont_rec',
                'r.esta_rec', 'r.estado_liquidacion', 'r.created_at',
                DB::raw("COALESCE(c.razon_social, CONCAT(c.nomb_per, ' ', c.pate_per, ' ', c.mate_per)) as cliente_nombre"),
                'c.documento as cliente_documento',
                'fp.descripcion as forma_pago'
            )
            ->where('r.vendedor_id', $vendedor->id)
            ->where('r.sede_id', $idsede);

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
                'cliente_nombre'     => $r->cliente_nombre,
                'cliente_documento'  => $r->cliente_documento,
                'forma_pago'         => $r->forma_pago,
                'created_at'         => $r->created_at,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'items'  => $items,
            'total'  => $items->count(),
        ]);
    }

    /**
     * GET /api/vendedor/historial-cobros/{id}/detalle
     */
    public function cobroDetalle($id)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }
        $vendedor = $this->resolveVendedor($user);
        $idsede = $user->sede_id;

        $recibo = DB::table('recibos as r')
            ->join('clientes as c', 'r.cliente_id', '=', 'c.id')
            ->leftJoin('forma_pagos as fp', function ($join) {
                $join->whereRaw('r.fpag_rec = CAST(fp.id AS varchar)');
            })
            ->where('r.id', $id)
            ->where('r.vendedor_id', $vendedor->id)
            ->where('r.sede_id', $idsede)
            ->select(
                'r.id', 'r.fech_rec', 'r.num_recibo', 'r.mont_rec',
                'r.esta_rec', 'r.estado_liquidacion', 'r.obse_rec',
                'r.created_at', 'r.vendedor_id',
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
            'status'   => true,
            'recibo'   => [
                'id'                 => (int) $recibo->id,
                'fecha'              => $recibo->fech_rec,
                'num_recibo'         => $recibo->num_recibo,
                'monto'              => (float) $recibo->mont_rec,
                'estado'             => $recibo->esta_rec,
                'estado_legible'     => $recibo->esta_rec == 'ANULADO' ? 'ANULADO' : 'EMITIDO',
                'estado_liquidacion' => $recibo->estado_liquidacion,
                'observacion'        => $recibo->obse_rec,
                'cliente_nombre'     => $recibo->cliente_nombre,
                'cliente_documento'  => $recibo->cliente_documento,
                'cliente_direccion'  => $recibo->cliente_direccion,
                'forma_pago'         => $recibo->forma_pago,
            ],
            'amortizaciones' => $amortizaciones,
            'total_amortizado' => $recibo->mont_rec,
        ]);
    }

    // =================== helpers ===================

    private function esVendedor($user): bool
    {
        return $user->roles()->where('id', 6)->exists()
            || $user->hasAnyRole(['VENDEDOR', 'COBRADOR']);
    }

    private function resolveVendedor($usuario)
    {
        $vendedor = Vendedor::where('usuario_id', $usuario->id)->first();
        if (!$vendedor) {
            $vendedor = Vendedor::create([
                'nombre'     => $usuario->name,
                'documento'  => '00000000',
                'direccion'  => 'Dirección por defecto',
                'usuario_id' => $usuario->id,
                'estado'     => 1,
            ]);
        }
        return $vendedor;
    }

    private function tipoEnvio($idsede)
    {
        $sede = Sede::find($idsede);
        return $sede ? $sede->tipo_envio : null;
    }
}
