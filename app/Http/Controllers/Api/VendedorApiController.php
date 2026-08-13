<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\servicios\FuncionesController;
use App\Vendedor;
use App\VendedorSector;
use App\Clientes;
use App\Venta;
use App\Recibos;
use App\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendedorApiController extends Controller
{
    /**
     * GET /api/vendedor/dashboard
     * Header: Authorization: Bearer {api_token}
     *
     * Devuelve las 5 métricas en un solo payload:
     *  - ventas.hoy        (total + cantidad)
     *  - cobranzas.hoy     (total + cantidad)
     *  - por_cobrar        (saldo pendiente de clientes de los sectores de hoy)
     *  - stock             (unidades netas + cantidad de items + detalle por producto)
     *  - sectores          (lista + total)
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'No autenticado.',
            ], 401);
        }

        if (!$user->esVendedorOCobrador()) {
            return response()->json([
                'status'  => false,
                'message' => 'Acceso denegado. Este endpoint es solo para vendedores.',
            ], 403);
        }

        $vendedor = $this->resolveVendedor($user);
        // Fecha a consultar: por defecto hoy. Si se pide una fecha distinta a hoy, se
        // muestra TODO lo de ese día (liquidado o no) en vez de solo lo pendiente —
        // para "hoy" se mantiene el criterio de "cuánto falta rendir".
        $fecha  = $request->input('fecha', date('Y-m-d'));
        $esHoy  = $fecha === date('Y-m-d');
        $idsede = $user->sede_id ?? 0;

        // 1) Sectores asignados en la fecha consultada
        $sectoresAsignados = VendedorSector::where('vendedor_id', $user->id)
            ->where('fecha', $fecha)
            ->with('sector.zona')
            ->get()
            ->map(function ($vs) {
                return [
                    'sector_id'   => (int) $vs->sector_id,
                    'nombre'      => optional($vs->sector)->nomb_sec,
                    'tipo'        => $vs->tipo ?? 'AMBOS',
                    'zona_nombre' => optional(optional($vs->sector)->zona)->nomb_zona,
                ];
            });

        // 2) Ventas de la fecha consultada (solo pendientes si es hoy), separadas por contado/crédito
        $ventasBaseQuery = Venta::where('vendedor_id', $vendedor->id)
            ->where('fecha', $fecha)
            ->when($esHoy, function ($q) {
                $q->where('estado_liquidacion', 'PENDIENTE');
            });

        $ventasContadoQuery = (clone $ventasBaseQuery)->where('tipo_pago_id', '!=', 2);
        $totalVentasContado = (float) $ventasContadoQuery->sum('monto');
        $cantVentasContado  = (int) $ventasContadoQuery->count();

        $ventasCreditoQuery = (clone $ventasBaseQuery)->where('tipo_pago_id', 2);
        $totalVentasCredito = (float) $ventasCreditoQuery->sum('monto');
        $cantVentasCredito  = (int) $ventasCreditoQuery->count();

        // 2b) Cuota inicial cobrada en ventas al crédito de la fecha consultada (venta_formapago, no está en `recibos`)
        $inicialQuery = DB::table('venta_formapago as vf')
            ->join('ventas as v', 'v.id', '=', 'vf.venta_id')
            ->where('v.vendedor_id', $vendedor->id)
            ->where('v.fecha', $fecha)
            ->where('v.tipo_pago_id', 2)
            ->when($esHoy, function ($q) {
                $q->where('v.estado_liquidacion', 'PENDIENTE');
            });
        $totalInicial = (float) (clone $inicialQuery)->sum('vf.monto');
        $cantInicial  = (int) (clone $inicialQuery)->count();

        // 3) Cobranzas de la fecha consultada (solo pendientes si es hoy)
        $cobrosQuery = Recibos::where('vendedor_id', $vendedor->id)
            ->where('fech_rec', $fecha)
            ->when($esHoy, function ($q) {
                $q->where('estado_liquidacion', 'PENDIENTE');
            });
        $totalCobranzas = (float) $cobrosQuery->sum('mont_rec');
        $cantCobranzas  = (int) $cobrosQuery->count();

        // 4) Por cobrar (clientes activos de los sectores de la fecha consultada)
        $sectoresIds = $sectoresAsignados->pluck('sector_id')->all();
        $clientesIds = !empty($sectoresIds)
            ? Clientes::where('estado_per', '1')->whereIn('id_sector', $sectoresIds)->pluck('id')->all()
            : [];

        $totalPorCobrar = 0.0;
        if (!empty($clientesIds)) {
            $totalPorCobrar = (float) DB::table('cuotas as cu')
                ->join('creditos as c', 'cu.credito_id', '=', 'c.id')
                ->where('c.esta_cre', '1')
                ->whereIn('c.cliente_id', $clientesIds)
                ->where('cu.esta_cuo', 'PENDIENTE')
                ->sum('cu.saldo_cuo');
        }

        // 5) Stock (cargado - vendido) de la fecha consultada
        list($totalStockUnits, $totalStockItems, $stockDetalle) =
            $this->calcularStock($user, $vendedor, $idsede, $fecha);

        return response()->json([
            'status'   => true,
            'fecha'    => $fecha,
            'vendedor' => [
                'id'     => $user->id,
                'nombre' => $user->name,
            ],
            'sectores' => [
                'total' => $sectoresAsignados->count(),
                'lista' => $sectoresAsignados->values(),
            ],
            'ventas' => [
                'contado' => [
                    'total'    => round($totalVentasContado, 2),
                    'cantidad' => $cantVentasContado,
                ],
                'credito' => [
                    'total'    => round($totalVentasCredito, 2),
                    'cantidad' => $cantVentasCredito,
                ],
                'inicial_credito' => [
                    'total'    => round($totalInicial, 2),
                    'cantidad' => $cantInicial,
                ],
            ],
            'cobranzas' => [
                'total'    => round($totalCobranzas, 2),
                'cantidad' => $cantCobranzas,
            ],
            'por_cobrar' => round($totalPorCobrar, 2),
            'stock' => [
                'unidades' => $totalStockUnits,
                'items'    => $totalStockItems,
                'detalle'  => $stockDetalle,
            ],
        ]);
    }

    /**
     * GET /api/vendedor/stock
     * Solo el detalle de stock (útil para "pull-to-refresh" del card sin recargar todo).
     */
    public function stock(Request $request)
    {
        $user = Auth::user();

        if (!$user->esVendedorOCobrador()) {
            return response()->json([
                'status'  => false,
                'message' => 'Acceso denegado.',
            ], 403);
        }

        $vendedor = $this->resolveVendedor($user);
        $fechaHoy = date('Y-m-d');
        $idsede   = $user->sede_id ?? 0;

        list($totalStockUnits, $totalStockItems, $stockDetalle) =
            $this->calcularStock($user, $vendedor, $idsede, $fechaHoy);

        return response()->json([
            'status'   => true,
            'fecha'    => $fechaHoy,
            'unidades' => $totalStockUnits,
            'items'    => $totalStockItems,
            'detalle'  => $stockDetalle,
        ]);
    }

    // ================== helpers ==================

    private function resolveVendedor($usuario)
    {
        $vendedor = Vendedor::where('usuario_id', $usuario->id)->first();
        if (!$vendedor) {
            $vendedor = Vendedor::create([
                'nombre'    => $usuario->name,
                'documento' => '00000000',
                'direccion' => 'Dirección por defecto',
                'usuario_id' => $usuario->id,
                'estado'    => 1,
            ]);
            $this->asignarUbicacionDefault($vendedor, $usuario->sede_id);
        } elseif (!$vendedor->stock_location_id) {
            $this->asignarUbicacionDefault($vendedor, $usuario->sede_id);
        }
        return $vendedor;
    }

    private function asignarUbicacionDefault($vendedor, $idsede)
    {
        $almacen = Almacen::where('sede_id', $idsede)->first();
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

    private function calcularStock($user, $vendedor, $idsede, $fechaHoy): array
    {
        // Obtener stock disponible desde stock_vendedor
        // stock_vendedor.vendedor_id = users.id (no vendedores.id)
        $stockVendedor = DB::table('stock_vendedor')
            ->where('vendedor_id', $user->id)
            ->where('fecha_carga', $fechaHoy)
            ->where('estado', 1)
            ->get();

        $detalle     = [];
        $totalUnits  = 0;
        $totalItems  = 0;

        $nombres = [];
        $productIds = $stockVendedor->pluck('producto_id')->unique()->toArray();
        if (!empty($productIds)) {
            $nombres = DB::table('productos')
                ->whereIn('id', $productIds)
                ->pluck('nomb_pro', 'id')
                ->toArray();
        }

        // Agrupar por producto (puede haber múltiples registros FIFO)
        $grouped = $stockVendedor->groupBy('producto_id');
        foreach ($grouped as $prodId => $items) {
            $cantidad = (int) $items->sum('cantidad_disponible');
            $vendido  = (int) $items->sum('cantidad_vendida');
            if ($cantidad > 0) {
                $detalle[] = [
                    'producto_id' => (int) $prodId,
                    'nombre'      => $nombres[$prodId] ?? '',
                    'cargado'     => $vendido + $cantidad,
                    'vendido'     => $vendido,
                    'stock'       => $cantidad,
                ];
                $totalUnits += $cantidad;
                $totalItems++;
            }
        }

        return [$totalUnits, $totalItems, $detalle];
    }
}
