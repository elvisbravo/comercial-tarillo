<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\VentaController;
use App\Almacen;
use App\Clientes;
use App\Sector;
use App\Tipo_comprobantes;
use App\Tipo_documento;
use App\Vendedor;
use App\VendedorSector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaApiController extends Controller
{
    /**
     * GET /api/vendedor/venta
     * Devuelve todo lo necesario para la pantalla de venta del vendedor móvil:
     *  - productos cargados hoy con stock y precios (contado/crédito)
     *  - clientes activos de los sectores asignados hoy
     *  - catálogos: comprobantes, tipo documento, forma de pago, bancos, sectores
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $fechaHoy = date('Y-m-d');
        $sectoresIds = VendedorSector::where('vendedor_id', $user->id)
            ->where('fecha', $fechaHoy)
            ->pluck('sector_id')
            ->all();

        $productosHoy = $this->productosDelDia($user, $fechaHoy);

        return response()->json([
            'status'           => true,
            'fecha'            => $fechaHoy,
            'vendedor'         => [
                'id'     => optional($this->resolveVendedor($user))->id,
                'nombre' => optional($this->resolveVendedor($user))->nombre,
            ],
            'productos'            => $productosHoy['items'] ?? [],
            'moviles_ubicacion_id' => $productosHoy['_moviles_ubicacion_id'] ?? 0,
            'clientes'             => $this->clientesPorSectores($sectoresIds),
            'comprobantes'     => Tipo_comprobantes::whereIn('id', [1, 2, 9])->get(['id', 'descripcion as nombre']),
            'tipo_documentos'  => Tipo_documento::all(['id', 'nombre']),
            'forma_pagos'      => DB::table('forma_pagos')->orderBy('id')->get(['id', 'descripcion as nombre']),
            'bancos'           => DB::table('cuentas_bancarias as cb')
                ->join('bancos as b', 'b.id', '=', 'cb.banco_id')
                ->select('cb.id', 'b.nombre as banco', 'cb.cuenta_corriente as numero_cuenta', 'cb.cuenta_cci as cci')
                ->get(),
            'sectores'         => Sector::where('estado', 'ACTIVO')
                ->get(['id', 'nomb_sec'])
                ->map(fn ($s) => ['id' => (int) $s->id, 'nombre' => $s->nomb_sec])
                ->values(),
        ]);
    }

    /**
     * GET /api/vendedor/clientes/buscar?q=texto
     * Busca clientes dentro de los sectores del vendedor (autocomplete).
     */
    public function buscarClientes(Request $request)
    {
        $user     = Auth::user();
        $termino  = trim($request->get('q', ''));
        $fechaHoy = date('Y-m-d');

        $sectoresIds = VendedorSector::where('vendedor_id', $user->id)
            ->where('fecha', $fechaHoy)
            ->pluck('sector_id')
            ->all();

        if (empty($sectoresIds)) {
            return response()->json(['status' => true, 'clientes' => []]);
        }

        $query = Clientes::where('estado_per', '1')
            ->whereIn('id_sector', $sectoresIds);

        if ($termino !== '') {
            $query->where(function ($q) use ($termino) {
                $q->where('razon_social', 'like', "%$termino%")
                  ->orWhere('nomb_per', 'like', "%$termino%")
                  ->orWhere('pate_per', 'like', "%$termino%")
                  ->orWhere('mate_per', 'like', "%$termino%")
                  ->orWhere('documento', 'like', "%$termino%");
            });
        }

        $clientes = $query->orderBy('razon_social')
            ->limit(20)
            ->get()
            ->map(fn ($c) => $this->mapCliente($c))
            ->values();

        return response()->json(['status' => true, 'clientes' => $clientes]);
    }

    /**
     * POST /api/vendedor/venta/guardar
     * Body: el mismo que /pos (ver VentaController@generar_venta).
     * Inyecta es_movil=true y resuelve el vendedor por la sesión.
     */
    public function guardar(Request $request)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $vendedor = $this->resolveVendedor($user);
        $request->merge([
            'es_movil' => true,
            'vendedor' => $vendedor->id,
        ]);

        return (new VentaController)->generar_venta($request);
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
                'nombre'    => $usuario->name,
                'documento' => '00000000',
                'direccion' => 'Dirección por defecto',
                'usuario_id' => $usuario->id,
                'estado'    => 1,
            ]);
            $almacen = Almacen::where('sede_id', $usuario->sede_id)->first();
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

    private function mapCliente($c): array
    {
        $nombreCompleto = trim(($c->nomb_per ?? '') . ' ' . ($c->pate_per ?? '') . ' ' . ($c->mate_per ?? ''));
        return [
            'id'            => (int) $c->id,
            'documento'     => $c->documento,
            'tipo_doc'      => $c->tipo_doc,
            'nombre'        => $nombreCompleto !== '' ? $nombreCompleto : ($c->razon_social ?? ''),
            'razon_social'  => $c->razon_social,
            'direccion'     => $c->dire_per,
            'telefono'      => $c->telefono,
            'sector_id'     => (int) $c->id_sector,
        ];
    }

    private function clientesPorSectores(array $sectoresIds): array
    {
        if (empty($sectoresIds)) {
            return [];
        }
        return Clientes::where('estado_per', '1')
            ->whereIn('id_sector', $sectoresIds)
            ->orderBy('razon_social')
            ->get()
            ->map(fn ($c) => $this->mapCliente($c))
            ->values()
            ->all();
    }

    private function productosDelDia($user, $fechaHoy): array
    {
        $idsede = $user->sede_id;
        $almacen = Almacen::where('sede_id', $idsede)->first();
        $ubicacion = $almacen
            ? DB::table('stock_location')
                ->where('almacen_id', $almacen->id)
                ->where(DB::raw('LOWER(name)'), 'moviles')
                ->first()
            : null;

        $movilesUbicacionId = $ubicacion ? (int) $ubicacion->id : 0;

        if (!$ubicacion) {
            return ['_moviles_ubicacion_id' => $movilesUbicacionId, 'items' => []];
        }

        $items = DB::table('detalle_traslado as dt')
            ->join('traslados as t', 't.id', '=', 'dt.traslado_id')
            ->join('productos as p', 'p.id', '=', 'dt.producto_id')
            ->leftJoin('detalle_almacen_productos as dp', function ($j) use ($ubicacion) {
                $j->on('dp.producto_id', '=', 'p.id')
                  ->where('dp.ubicacion_id', '=', $ubicacion->id);
            })
            ->leftJoin('precios as pr', 'pr.articulo_id', '=', 'p.id')
            ->where('t.serie', 'CAR')
            ->where('t.cliente_id', $user->id)
            ->where('t.fecha', $fechaHoy)
            ->where('t.estado', 1)
            ->where('dt.estado', 1)
            ->where('p.estado', 1)
            ->where(function ($q) {
                $q->whereNull('dp.stock')->orWhere('dp.stock', '>', 0);
            })
            ->select(
                'p.id',
                'p.nomb_pro as nombre',
                'p.codigo_barras',
                DB::raw('COALESCE(dp.stock, 0) as stock'),
                'pr.precio_contado',
                'pr.precio_credito'
            )
            ->orderBy('p.nomb_pro')
            ->get()
            ->map(function ($p) {
                return [
                    'id'             => (int) $p->id,
                    'nombre'         => $p->nombre,
                    'codigo_barras'  => $p->codigo_barras,
                    'stock'          => (int) $p->stock,
                    'precio_contado' => (float) ($p->precio_contado ?? 0),
                    'precio_credito' => (float) ($p->precio_credito ?? 0),
                ];
            })
            ->values()
            ->all();

        return [
            '_moviles_ubicacion_id' => $movilesUbicacionId,
            'items'                 => $items,
        ];
    }
}
