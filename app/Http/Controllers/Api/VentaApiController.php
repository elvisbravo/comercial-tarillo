<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\servicios\FuncionesController;
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
            'comprobantes'     => Tipo_comprobantes::whereIn('id', [1, 2, 5])->get(['id', 'descripcion as nombre']),
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
        $termino  = trim($request->get('q', ''));

        \Log::info('buscarClientes', ['termino' => $termino]);

        $query = Clientes::where('estado_per', '1');

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

        \Log::info('buscarClientes resultado', ['count' => $clientes->count()]);

        return response()->json(['status' => true, 'clientes' => $clientes]);
    }

    /**
     * POST /api/vendedor/venta/guardar
     * Body: el mismo que /pos (ver VentaController@generar_venta).
     * Inyecta es_movil=true y resuelve el vendedor por la sesión.
     * Soporta multipart con venta_data (JSON) + fotos (archivos).
     */
    public function guardar(Request $request)
    {
        $user = Auth::user();
        if (!$this->esVendedor($user)) {
            \Log::warning('API guardar venta - acceso denegado');
            return response()->json(['status' => false, 'message' => 'Acceso denegado.'], 403);
        }

        // Si es multipart (tiene venta_data), decodificar el JSON
        // IMPORTANTE: Guardar los archivos ANTES del merge, porque merge() pierde los archivos
        $fotosFilesRaw = $request->file('fotos');
        \Log::info('DEBUG archivos recibidos', [
            'fotosFilesRaw_type' => gettype($fotosFilesRaw),
            'fotosFilesRaw_is_array' => is_array($fotosFilesRaw),
            'fotosFilesRaw_count' => is_array($fotosFilesRaw) ? count($fotosFilesRaw) : 'not_array',
        ]);
        // Normalizar a array (Laravel puede retornar un solo UploadedFile cuando hay 1 archivo)
        // IMPORTANTE: cuando hay N archivos, Laravel puede retornar array de arrays si vienen de diferentes sources
        if (is_array($fotosFilesRaw)) {
            // Aplanar si hay arrays anidados
            $fotosFiles = [];
            foreach ($fotosFilesRaw as $item) {
                if (is_array($item)) {
                    foreach ($item as $subItem) {
                        if ($subItem) $fotosFiles[] = $subItem;
                    }
                } elseif ($item) {
                    $fotosFiles[] = $item;
                }
            }
        } else {
            $fotosFiles = $fotosFilesRaw ? [$fotosFilesRaw] : [];
        }
        $tieneFotos = !empty($fotosFiles);

        if ($request->has('venta_data')) {
            $ventaData = json_decode($request->input('venta_data'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['status' => false, 'message' => 'venta_data no es JSON válido.'], 400);
            }
            // Reemplazar los datos de la request con el JSON decodificado
            foreach ($ventaData as $key => $value) {
                $request->merge([$key => $value]);
            }
            \Log::info('API guardar venta - inicio', [
                'id_local' => $ventaData['id_local'] ?? null,
                'tipo_venta' => $ventaData['tipo_venta'] ?? null,
                'tiene_fotos' => $tieneFotos,
            ]);
        } else {
            \Log::info('API guardar venta - inicio', $request->all());
        }

        // Configurar la sesión para que VentaController pueda acceder a session('key')->sede_id
        session(['key' => $user]);

        $vendedor = $this->resolveVendedor($user);

        // Obtener ubicacion_id de moviles
        $idsede = $user->sede_id;
        $almacen = Almacen::where('sede_id', $idsede)->first();
        $ubicacion = $almacen
            ? DB::table('stock_location')
                ->where('almacen_id', $almacen->id)
                ->where(DB::raw('LOWER(name)'), 'moviles')
                ->first()
            : null;
        $movilesUbicacionId = $ubicacion ? (int) $ubicacion->id : 0;

        \Log::info('API guardar venta - movilesUbicacionId', ['movilesUbicacionId' => $movilesUbicacionId]);

        // El vendedor que envía la app es directamente users.id (stock_vendedor.vendedor_id = users.id)
        $request->merge([
            'es_movil' => true,
            'ubicacion_id' => $movilesUbicacionId,
        ]);

        // Asegurar que existe registro en detalle_almacen_productos para cada producto
        // (porque cargarStockProcesar no lo crea, solo el KARDEX)
        if ($movilesUbicacionId > 0) {
            $this->asegurarStockEnAlmacen($request, $movilesUbicacionId);
        }

        \Log::info('API guardar venta - antes de generar_venta');

        $response = (new VentaController)->generar_venta($request);

        \Log::info('API guardar venta - respuesta generar_venta', [
            'status' => $response->getStatusCode(),
            'body' => json_decode($response->getContent(), true)
        ]);

        // Procesar fotos si la venta fue exitosa (respuesta = ok)
        if ($response->getStatusCode() === 200) {
            $body = json_decode($response->getContent(), true);
            $respuesta = $body['respuesta'] ?? '';
            $ventaId = $body['id'] ?? null;

            \Log::info('DEBUG fotos antes de guardar', [
                'ventaId' => $ventaId,
                'tieneFotos' => $tieneFotos,
                'fotosFiles_count' => count($fotosFiles),
            ]);

            if ($ventaId && $tieneFotos && !empty($fotosFiles)) {
                $this->guardarFotos($ventaId, $fotosFiles);
            }
        }

        // NOTA: El descuento de stock se realiza dentro de VentaController@generar_venta
        // para ventas móviles (stock_vendedor). No duplicar aquí.

        return $response;
    }

    /**
     * Guarda las fotos de la venta en Storage y en la tabla venta_fotos.
     */
    private function guardarFotos(int $ventaId, $fotos)
    {
        \Log::info('guardarFotos iniciado', ['ventaId' => $ventaId, 'fotos_count' => count($fotos)]);
        try {
            foreach ($fotos as $index => $foto) {
                if ($foto && $foto->isValid()) {
                    $extension = $foto->getClientOriginalExtension() ?: 'jpg';
                    $fileName = "venta_foto_{$ventaId}_{$index}_" . time() . ".$extension";
                    $path = $foto->storeAs('venta_fotos', $fileName, 'public');
                    \App\VentaFoto::create([
                        'venta_id' => $ventaId,
                        'foto_path' => $path,
                        'tipo_foto' => 'cliente',
                    ]);
                    \Log::info('Foto guardada', ['venta_id' => $ventaId, 'path' => $path]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error al guardar fotos', [
                'venta_id' => $ventaId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Asegura que existe registro en detalle_almacen_productos para la ubicación moviles
     * SIEMPRE sincroniza el stock desde stock_vendedor (crea o actualiza)
     */
    private function asegurarStockEnAlmacen(Request $request, $movilesUbicacionId)
    {
        if ($movilesUbicacionId == 0) {
            \Log::warning('asegurarStockEnAlmacen - movilesUbicacionId es 0, saliendo');
            return;
        }

        $servicios = new FuncionesController;
        $envio = $servicios->tipo_envio_sunat();

        $productos = $request->input('idproducto', []);
        $fechaHoy = date('Y-m-d');
        $vendedorId = $request->input('vendedor');

        \Log::info('asegurarStockEnAlmacen', [
            'productos' => $productos,
            'vendedorId' => $vendedorId,
            'envio' => $envio
        ]);

        for ($i = 0; $i < count($productos); $i++) {
            $productoId = $productos[$i];

            // Obtener stock actual desde stock_vendedor
            $stockVendedor = DB::table('stock_vendedor')
                ->where('vendedor_id', $vendedorId)
                ->where('producto_id', $productoId)
                ->where('fecha_carga', $fechaHoy)
                ->where('estado', 1)
                ->first();

            $stockActual = $stockVendedor ? ($stockVendedor->cantidad_disponible ?? 0) : 0;

            \Log::info('Producto', [
                'productoId' => $productoId,
                'stockVendedor' => $stockVendedor ? (array)$stockVendedor : null,
                'stockActual' => $stockActual
            ]);

            // Buscar registro existente
            $existe = DB::table('detalle_almacen_productos')
                ->where('ubicacion_id', $movilesUbicacionId)
                ->where('producto_id', $productoId)
                ->first();

            if ($existe) {
                // Actualizar stock existente
                DB::table('detalle_almacen_productos')
                    ->where('id', $existe->id)
                    ->update([
                        'stock' => $stockActual,
                        'tipo_envio' => $envio,
                        'updated_at' => now(),
                    ]);
                \Log::info('Stock actualizado en detalle_almacen_productos', ['stock' => $stockActual]);
            } else {
                // Crear nuevo registro solo si hay stock
                if ($stockActual > 0) {
                    DB::table('detalle_almacen_productos')->insert([
                        'ubicacion_id' => $movilesUbicacionId,
                        'producto_id' => $productoId,
                        'stock' => $stockActual,
                        'tipo_envio' => $envio,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    \Log::info('Stock creado en detalle_almacen_productos', ['stock' => $stockActual]);
                } else {
                    \Log::warning('Stock es 0, no se crea registro');
                }
            }
        }
    }

    /**
     * Actualiza stock_vendedor después de una venta exitosa
     */
    private function actualizarStockVendedor($vendedorUserId, $data)
    {
        $productos = $data['idproducto'] ?? [];
        $cantidades = $data['quanty'] ?? [];

        for ($i = 0; $i < count($productos); $i++) {
            $productoId = $productos[$i];
            $cantidad = $cantidades[$i];

            // Buscar registro en stock_vendedor para este vendedor y producto del día
            $stockVendedor = DB::table('stock_vendedor')
                ->where('vendedor_id', $vendedorUserId)
                ->where('producto_id', $productoId)
                ->where('fecha_carga', date('Y-m-d'))
                ->where('estado', 1)
                ->first();

            if ($stockVendedor) {
                // Actualizar cantidades
                DB::table('stock_vendedor')
                    ->where('id', $stockVendedor->id)
                    ->update([
                        'cantidad_vendida' => ($stockVendedor->cantidad_vendida ?? 0) + $cantidad,
                        'cantidad_disponible' => max(0, ($stockVendedor->cantidad_disponible ?? 0) - $cantidad),
                    ]);
            }
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

        $vendedor = $this->resolveVendedor($user);

        // Productos del stock_vendedor (misma lógica que vendedorVenta web)
        $productos = DB::table('stock_vendedor as sv')
            ->join('productos as p', 'p.id', '=', 'sv.producto_id')
            ->leftJoin('precios as pr', 'pr.articulo_id', '=', 'p.id')
            ->select(
                'p.id',
                'p.nomb_pro as nombre',
                'p.codigo_barras',
                DB::raw('SUM(sv.cantidad_disponible) as stock'),
                'pr.precio_contado',
                'pr.precio_credito'
            )
            ->where('sv.vendedor_id', '=', $user->id)
            ->where('sv.fecha_carga', '=', $fechaHoy)
            ->where('sv.estado', '=', 1)
            ->where('p.estado', '=', '1')
            ->groupBy('p.id', 'p.nomb_pro', 'p.codigo_barras', 'pr.precio_contado', 'pr.precio_credito')
            ->orderBy('p.nomb_pro', 'asc')
            ->get();

        // Filtrar productos con stock > 0
        $items = $productos->filter(fn ($p) => $p->stock > 0)->map(function ($p) use ($movilesUbicacionId) {
            return [
                'id'             => (int) $p->id,
                'nombre'         => $p->nombre,
                'codigo_barras'  => $p->codigo_barras,
                'stock'          => (int) $p->stock,
                'precio_contado' => (float) ($p->precio_contado ?? 0),
                'precio_credito' => (float) ($p->precio_credito ?? 0),
                'ubicacion_id'   => $movilesUbicacionId,
            ];
        })->values()->all();

        return [
            '_moviles_ubicacion_id' => $movilesUbicacionId,
            'items'                 => $items,
        ];
    }
}
