<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AmortizacionesController;
use App\Clientes;
use App\Creditos;
use App\Cuotas;
use App\Sector;
use App\Vendedor;
use App\VendedorSector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CobrosApiController extends Controller
{
    /**
     * GET /api/vendedor/cobros
     * Devuelve los créditos activos de los clientes en sectores del vendedor,
     * con saldo pendiente, próximas cuotas y estado.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $fechaHoy = date('Y-m-d');
        $vendedor = $this->resolveVendedor($user);

        $sectoresIds = VendedorSector::where('vendedor_id', $user->id)
            ->where('fecha', $fechaHoy)
            ->pluck('sector_id')
            ->all();

        $clientesIds = Clientes::where('estado_per', '1')
            ->whereIn('id_sector', $sectoresIds)
            ->pluck('id')
            ->all();

        $creditos = collect();
        if (!empty($clientesIds)) {
            $raw = DB::table('creditos as c')
                ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
                ->select(
                    'c.id',
                    'c.cliente_id',
                    'c.mont_cre',
                    'c.fech_cre',
                    'c.fpag_cre',
                    'c.peri_cre',
                    'cl.razon_social',
                    'cl.documento',
                    'cl.dire_per',
                    'cl.telefono',
                    'cl.id_sector as sector_id'
                )
                ->where('c.esta_cre', '1')
                ->whereNull('c.f_anulacion')
                ->whereIn('c.cliente_id', $clientesIds)
                ->orderBy('cl.razon_social')
                ->get();

            $creditos = $raw->map(function ($c) use ($fechaHoy) {
                $cuotas = DB::table('cuotas')
                    ->where('credito_id', $c->id)
                    ->orderBy('numero_cuo', 'asc')
                    ->get();

                $pendientes = $cuotas->where('esta_cuo', 'PENDIENTE');
                $pagadas    = $cuotas->where('esta_cuo', 'COBRADA');
                $vencidas   = $pendientes->filter(fn ($q) => $q->fven_cuo < $fechaHoy)->values();

                $saldo = (float) $pendientes->sum('saldo_cuo');
                $proxima = $pendientes->sortBy('fven_cuo')->first();

                $estado = 'PAGADO';
                if ($saldo > 0) {
                    $estado = $vencidas->isNotEmpty() ? 'MOROSO' : 'AL_DIA';
                }

                return [
                    'id'              => (int) $c->id,
                    'cliente_id'      => (int) $c->cliente_id,
                    'monto_total'     => (float) $c->mont_cre,
                    'fecha_credito'   => $c->fech_cre,
                    'fecha_pago'      => $c->fpag_cre,
                    'periodo'         => $c->peri_cre,
                    'saldo_pendiente' => $saldo,
                    'cuotas_totales'  => $cuotas->count(),
                    'cuotas_pagadas'  => $pagadas->count(),
                    'cuotas_vencidas' => $vencidas->count(),
                    'proxima_cuota'   => $proxima ? [
                        'numero'      => (int) $proxima->numero_cuo,
                        'vencimiento' => $proxima->fven_cuo,
                        'monto'       => (float) $proxima->mont_cuo,
                        'saldo'       => (float) $proxima->saldo_cuo,
                    ] : null,
                    'estado'          => $estado,
                    'cliente'         => [
                        'id'        => (int) $c->cliente_id,
                        'nombre'    => $c->razon_social,
                        'documento' => $c->documento,
                        'direccion' => $c->dire_per,
                        'telefono'  => $c->telefono,
                        'sector_id' => (int) $c->sector_id,
                    ],
                ];
            })->values();
        }

        return response()->json([
            'status'       => true,
            'fecha'        => $fechaHoy,
            'vendedor'     => [
                'id'     => $vendedor->id,
                'nombre' => $vendedor->nombre,
            ],
            'creditos'     => $creditos,
            'forma_pagos'  => DB::table('forma_pagos')->orderBy('id')->get(['id', 'descripcion as nombre']),
            'bancos'       => DB::table('cuentas_bancarias as cb')
                ->join('bancos as b', 'b.id', '=', 'cb.banco_id')
                ->select('cb.id', 'b.nombre as banco', 'cb.cuenta_corriente as numero_cuenta', 'cb.cuenta_cci as cci')
                ->get(),
            'sectores'     => Sector::where('estado', 'ACTIVO')
                ->get(['id', 'nomb_sec'])
                ->map(fn ($s) => ['id' => (int) $s->id, 'nombre' => $s->nomb_sec])
                ->values(),
        ]);
    }

    /**
     * GET /api/vendedor/cobros/{id}/detalle
     * Devuelve el detalle completo de un crédito: cliente, productos vendidos
     * y todas las cuotas (con estado).
     */
    public function detalle($id)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $credito = DB::table('creditos as c')
            ->join('clientes as cl', 'c.cliente_id', '=', 'cl.id')
            ->where('c.id', $id)
            ->select(
                'c.*',
                'cl.razon_social as cliente_nombre',
                'cl.documento as cliente_documento',
                'cl.dire_per as cliente_direccion',
                'cl.telefono as cliente_telefono'
            )
            ->first();

        if (!$credito) {
            return response()->json(['status' => false, 'message' => 'Crédito no encontrado.'], 404);
        }

        $productos = DB::table('detalle_venta as dv')
            ->join('productos as p', 'p.id', '=', 'dv.producto_id')
            ->where('dv.venta_id', $credito->id_venta)
            ->select('p.id', 'p.nomb_pro as nombre', 'dv.cantidad', 'dv.precio', 'dv.subtotal as importe')
            ->get();

        $cuotas = DB::table('cuotas')
            ->where('credito_id', $id)
            ->orderBy('numero_cuo', 'asc')
            ->get()
            ->map(fn ($q) => [
                'id'          => (int) $q->id,
                'numero'      => (int) $q->numero_cuo,
                'vencimiento' => $q->fven_cuo,
                'monto'       => (float) $q->mont_cuo,
                'capital'     => (float) $q->capi_cuo,
                'saldo'       => (float) $q->saldo_cuo,
                'estado'      => $q->esta_cuo,
            ]);

        return response()->json([
            'status'     => true,
            'credito'    => [
                'id'              => (int) $credito->id,
                'cliente_id'      => (int) $credito->cliente_id,
                'monto_total'     => (float) $credito->mont_cre,
                'saldo_pendiente' => (float) DB::table('cuotas')
                                            ->where('credito_id', $id)
                                            ->where('esta_cuo', 'PENDIENTE')
                                            ->sum('saldo_cuo'),
                'fecha_credito'   => $credito->fech_cre,
                'fecha_pago'      => $credito->fpag_cre,
                'periodo'         => $credito->peri_cre,
                'observacion'     => $credito->obse_cre,
                'estado'          => $credito->esta_cre,
            ],
            'cliente'    => [
                'id'        => (int) $credito->cliente_id,
                'nombre'    => $credito->cliente_nombre,
                'documento' => $credito->cliente_documento,
                'direccion' => $credito->cliente_direccion,
                'telefono'  => $credito->cliente_telefono,
            ],
            'productos'  => $productos->map(fn ($p) => [
                'id'       => (int) $p->id,
                'nombre'   => $p->nombre,
                'cantidad' => (float) $p->cantidad,
                'precio'   => (float) $p->precio,
                'importe'  => (float) $p->importe,
            ]),
            'cuotas'     => $cuotas,
        ]);
    }

    /**
     * POST /api/vendedor/cobros/guardar
     * Body: credito_id, cliente_id, mont_rec, fech_rec, fpag_rec, obse_rec?, docu_ref?
     * Delega en AmortizacionesController@crear (misma lógica de negocio).
     */
    public function guardar(Request $request)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $vendedor = $this->resolveVendedor($user);

        $request->merge([
            'es_movil'    => true,
            'vendedor_id' => $vendedor->id,
            'forma_pago_id' => $request->forma_pago_id ?? 1, // Default a 1 (Efectivo) si no se envía
        ]);

        // Inyectar la sesión "key" para que AmortizacionesController@crear
        // (que usa session('key')->sede_id) funcione en el contexto de la API.
        session(['key' => $user]);

        $rules = [
            'credito_id' => 'required|integer',
            'cliente_id' => 'required|integer',
            'mont_rec'   => 'required|numeric|min:0.01',
            'fech_rec'   => 'required|date',
            'fpag_rec'   => 'required|date',
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $resp = (new AmortizacionesController)->crear($request);
            $data = $resp->getData(true);

            if (isset($data['status']) && $data['status'] === 'OK') {
                $creditoId = (int) $request->credito_id;
                $saldo = (float) DB::table('cuotas')
                    ->where('credito_id', $creditoId)
                    ->where('esta_cuo', 'PENDIENTE')
                    ->sum('saldo_cuo');

                return response()->json([
                    'status'         => true,
                    'message'        => 'Cobranza registrada',
                    'recibo_id'      => $data['id'] ?? null,
                    'saldo_restante' => $saldo,
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => is_array($data) ? ($data['message'] ?? 'No se pudo registrar la cobranza') : 'No se pudo registrar la cobranza',
                'detalle' => $data,
            ], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('cobros/guardar exception', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
            $almacen = \App\Almacen::where('sede_id', $usuario->sede_id)->first();
            if ($almacen) {
                $default = DB::table('stock_location')
                    ->where('almacen_id', $almacen->id)
                    ->where('name', '!=', 'Transferencias')
                    ->orderByRaw("CASE WHEN name = 'Stock' THEN 1 ELSE 2 END")
                    ->first();
                if ($default) {
                    $vendedor->stock_location_id = $default->id;
                    $vendedor->save();
                }
            }
        }
        return $vendedor;
    }
}
