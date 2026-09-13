<?php

namespace App\Http\Controllers;

use App\Almacen;
use App\Creditos;
use App\Cuotas;
use App\Detalle_traslado;
use App\RecojoMercaderia;
use App\Traslado;
use App\Http\Controllers\servicios\FuncionesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RecojoMercaderiaController extends Controller
{
    const MOTIVO_RECOJO_MERCADERIA = 'RECOJO DE MERCADERIA POR CREDITO INCOBRABLE';

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rolesVendedorCobradorIds()
    {
        return Role::whereIn('name', ['VENDEDOR (a)', 'COBRADOR (a)', 'COBRADOR (a) / VENDEDOR (a)'])->pluck('id');
    }

    public function index(Request $request)
    {
        $idsede = session('key')->sede_id;

        $rolesIds = $this->rolesVendedorCobradorIds();
        $vendedores = \App\User::where('sede_id', $idsede)
            ->where('estado', 1)
            ->whereHas('roles', function ($q) use ($rolesIds) {
                $q->whereIn('id', $rolesIds);
            })->orderBy('name', 'asc')->get();

        // Filtro de fecha del historial: por defecto, del último mes hasta hoy.
        $fechaDesde = $request->input('fecha_desde', date('Y-m-d', strtotime('-1 month')));
        $fechaHasta = $request->input('fecha_hasta', date('Y-m-d'));

        $historial = RecojoMercaderia::with(['cliente', 'vendedorRecojo', 'usuario'])
            ->where('sede_id', $idsede)
            ->where('fecha', '>=', $fechaDesde)
            ->when($fechaHasta, function ($q) use ($fechaHasta) {
                $q->where('fecha', '<=', $fechaHasta);
            })
            ->orderBy('fecha', 'desc')
            ->get();

        return view('ventas_moviles.recojo_mercaderia', compact('vendedores', 'historial', 'fechaDesde', 'fechaHasta'));
    }

    // Búsqueda de clientes con al menos un crédito activo en la sede, por documento o nombre
    public function buscarCliente(Request $request)
    {
        $idsede = session('key')->sede_id;
        $q = trim((string) $request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $clientes = DB::table('clientes as cl')
            ->join('creditos as cr', 'cr.cliente_id', '=', 'cl.id')
            ->select('cl.id', DB::raw("COALESCE(cl.razon_social, cl.nomb_per, 'Cliente') as nombre"), 'cl.documento')
            ->where('cr.sede_id', $idsede)
            ->where('cr.esta_cre', 1)
            ->where(function ($qq) use ($q) {
                $qq->where('cl.nomb_per', 'ilike', "%{$q}%")
                   ->orWhere('cl.razon_social', 'ilike', "%{$q}%")
                   ->orWhere('cl.documento', 'ilike', "%{$q}%");
            })
            ->distinct()
            ->limit(20)
            ->get();

        return response()->json($clientes);
    }

    // Créditos activos de un cliente, con su saldo pendiente
    public function creditosDelCliente($clienteId)
    {
        $idsede = session('key')->sede_id;

        $creditos = Creditos::where('cliente_id', $clienteId)
            ->where('sede_id', $idsede)
            ->where('esta_cre', 1)
            ->orderBy('fech_cre', 'desc')
            ->get()
            ->map(function ($c) {
                $saldo = Cuotas::where('credito_id', $c->id)
                    ->where('esta_cuo', 'PENDIENTE')
                    ->sum('saldo_cuo');
                return [
                    'id' => $c->id,
                    'monto' => (float) $c->mont_cre,
                    'saldo_pendiente' => (float) $saldo,
                    'fecha' => $c->fech_cre,
                ];
            });

        return response()->json($creditos);
    }

    // Cantidad ya recuperada de un producto en recojos anteriores del mismo crédito
    private function cantidadYaRecuperada($creditoId, $productoId)
    {
        return (float) DB::table('recojos_mercaderia as rm')
            ->join('detalle_traslado as dt', 'dt.traslado_id', '=', 'rm.traslado_id')
            ->where('rm.credito_id', $creditoId)
            ->where('dt.producto_id', $productoId)
            ->sum('dt.cantidad');
    }

    // Detalle de un crédito: productos vendidos (para elegir cuáles se recuperan) y cuotas pendientes
    public function detalleCredito($creditoId)
    {
        $idsede = session('key')->sede_id;

        $credito = Creditos::where('id', $creditoId)->where('sede_id', $idsede)->first();
        if (!$credito) {
            abort(403, 'Crédito no encontrado en esta sede.');
        }

        $productos = DB::table('detalle_venta as dv')
            ->join('productos as p', 'p.id', '=', 'dv.producto_id')
            ->where('dv.venta_id', $credito->id_venta)
            ->select('p.id as producto_id', 'p.nomb_pro', 'dv.cantidad as cantidad_vendida', 'dv.precio')
            ->get()
            ->map(function ($p) use ($creditoId) {
                $yaRecuperada = $this->cantidadYaRecuperada($creditoId, $p->producto_id);
                $p->cantidad_disponible = max(0, (float) $p->cantidad_vendida - $yaRecuperada);
                return $p;
            });

        $cuotasPendientes = Cuotas::where('credito_id', $credito->id)->where('esta_cuo', 'PENDIENTE')->get();

        return response()->json([
            'credito_id' => $credito->id,
            'productos' => $productos,
            'cuotas_pendientes' => $cuotasPendientes->count(),
            'saldo_pendiente' => (float) $cuotasPendientes->sum('saldo_cuo'),
        ]);
    }

    // Detalle de un recojo ya registrado (para el historial): productos recuperados, si los hubo
    public function verDetalle($id)
    {
        $idsede = session('key')->sede_id;

        $recojo = RecojoMercaderia::with(['cliente', 'vendedorRecojo', 'usuario'])
            ->where('id', $id)
            ->where('sede_id', $idsede)
            ->first();

        if (!$recojo) {
            return response()->json(['error' => 'Registro no encontrado.'], 404);
        }

        $productos = [];
        if ($recojo->traslado_id) {
            $productos = DB::table('detalle_traslado as dt')
                ->join('productos as p', 'p.id', '=', 'dt.producto_id')
                ->select('p.nomb_pro', 'dt.cantidad')
                ->where('dt.traslado_id', $recojo->traslado_id)
                ->get();
        }

        return response()->json([
            'fecha' => $recojo->fecha,
            'cliente_nombre' => optional($recojo->cliente)->razon_social ?: optional($recojo->cliente)->nomb_per,
            'vendedor_recojo_nombre' => optional($recojo->vendedorRecojo)->name,
            'usuario_nombre' => optional($recojo->usuario)->name,
            'saldo_incobrable' => (float) $recojo->saldo_incobrable,
            'valor_recuperado' => (float) $recojo->valor_recuperado,
            'credito_cerrado' => (bool) $recojo->credito_cerrado,
            'observacion' => $recojo->observacion,
            'productos' => $productos,
        ]);
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'vendedor_recojo_id' => 'required|exists:users,id',
            'productos' => 'array',
            'productos.*.producto_id' => 'required_with:productos|exists:productos,id',
            'productos.*.cantidad' => 'required_with:productos|numeric|min:0',
            'observacion' => 'nullable|string',
            'cerrar_credito' => 'nullable|boolean',
        ]);

        $idsede = session('key')->sede_id;

        $credito = Creditos::where('id', $request->credito_id)->where('sede_id', $idsede)->first();
        if (!$credito) {
            abort(403, 'Crédito no encontrado en esta sede.');
        }
        if ((int) $credito->esta_cre !== 1) {
            return redirect()->back()->with('error', 'Este crédito ya no está activo (puede que ya se haya procesado un recojo).');
        }

        $perteneceASede = \App\User::where('id', $request->vendedor_recojo_id)
            ->where('sede_id', $idsede)
            ->exists();
        if (!$perteneceASede) {
            abort(403, 'El vendedor seleccionado no pertenece a su sede.');
        }

        $productosRecuperados = collect($request->input('productos', []))
            ->filter(function ($p) {
                return (float) ($p['cantidad'] ?? 0) > 0;
            })
            ->values();

        // Validar server-side que no se recupere más de lo realmente disponible (evita doble recojo del mismo producto)
        // y valorizar lo recuperado al precio de venta registrado en esta venta
        $valorRecuperado = 0.0;
        foreach ($productosRecuperados as $item) {
            $ventaLinea = DB::table('detalle_venta')
                ->where('venta_id', $credito->id_venta)
                ->where('producto_id', $item['producto_id'])
                ->first();
            if (!$ventaLinea) {
                return redirect()->back()->with('error', 'Uno de los productos no pertenece a la venta de este crédito.');
            }
            $yaRecuperada = $this->cantidadYaRecuperada($credito->id, $item['producto_id']);
            $disponible = max(0, (float) $ventaLinea->cantidad - $yaRecuperada);
            if ((float) $item['cantidad'] > $disponible) {
                return redirect()->back()->with('error', 'La cantidad a recuperar de "' . $item['producto_id'] . '" excede lo disponible. Recargue la página e intente de nuevo.');
            }
            $valorRecuperado += (float) $item['cantidad'] * (float) $ventaLinea->precio;
        }

        DB::beginTransaction();

        try {
            $servicios = new FuncionesController;
            $envio = $servicios->tipo_envio_sunat();
            $user_id = Auth::user()->id;
            $trasladoId = null;

            if ($productosRecuperados->isNotEmpty()) {
                $almacenPrincipal = Almacen::where('sede_id', $idsede)->first();
                if (!$almacenPrincipal) {
                    throw new \RuntimeException('No se encontró el almacén principal de la sede.');
                }

                $ubicacionDestino = DB::table('stock_location')
                    ->where('almacen_id', $almacenPrincipal->id)
                    ->where('name', 'Stock')
                    ->first();
                if (!$ubicacionDestino) {
                    throw new \RuntimeException("No se encontró la ubicación de stock principal 'Stock'.");
                }
                $destino_id = $ubicacionDestino->id;

                $correlativoRecord = DB::table('correlativos')
                    ->where('sede_id', $idsede)
                    ->where('tipo_envio', $envio)
                    ->where('tipo_comprobante_id', 7) // GUIA INTERNA
                    ->first();
                if (!$correlativoRecord) {
                    throw new \RuntimeException('No existe correlativo configurado para GUIA INTERNA en esta sede.');
                }
                $nuevoCorrelativo = (int) $correlativoRecord->correlativo + 1;
                DB::table('correlativos')->where('id', $correlativoRecord->id)->update(['correlativo' => $nuevoCorrelativo]);

                $traslado = new Traslado;
                $traslado->fecha = date('Y-m-d');
                $traslado->hora = date('H:i:s');
                $traslado->serie = $correlativoRecord->serie;
                $traslado->correlativo = $nuevoCorrelativo;
                $traslado->almacen_origen = $almacenPrincipal->id;
                $traslado->almacen_destino = $almacenPrincipal->id;
                $traslado->id_ubicacion_origen = $destino_id;
                $traslado->id_ubicacion_destino = $destino_id;
                $traslado->motivo = self::MOTIVO_RECOJO_MERCADERIA;
                $traslado->estado = 1;
                $traslado->tipo_envio = $envio;
                $traslado->sede_id = $idsede;
                $traslado->user_id = $user_id;
                $traslado->user_recepcion = $user_id;
                $traslado->fecha_recibido = date('Y-m-d');
                $traslado->hora_recibido = date('H:i:s');
                $traslado->save();

                foreach ($productosRecuperados as $item) {
                    $productId = $item['producto_id'];
                    $cantidad = (float) $item['cantidad'];

                    $detalle = new Detalle_traslado;
                    $detalle->producto_id = $productId;
                    $detalle->traslado_id = $traslado->id;
                    $detalle->cantidad = $cantidad;
                    $detalle->cantidad_recibido = $cantidad;
                    $detalle->diferencia = 0;
                    $detalle->estado = 1;
                    $detalle->save();

                    $precio_unitario = DB::table('precios')->where('articulo_id', $productId)->value('precio_contado') ?? 0;

                    $servicios->aumentar_descontar_stock(1, $destino_id, $productId, $cantidad, $envio);
                    $servicios->movimiento_kardex_producto(
                        $destino_id, $productId, $cantidad, 1,
                        self::MOTIVO_RECOJO_MERCADERIA, $traslado->serie, $traslado->correlativo,
                        $precio_unitario, 9, date('Y-m-d'), date('Y-m-d')
                    );
                }

                $trasladoId = $traslado->id;
            }

            // (las cuotas ya cobradas nunca se tocan en ninguno de los dos casos siguientes)
            $cuotasPendientes = Cuotas::where('credito_id', $credito->id)->where('esta_cuo', 'PENDIENTE')->orderBy('fven_cuo')->get();
            $saldoPendienteActual = (float) $cuotasPendientes->sum('saldo_cuo');
            $cerrarManual = $request->boolean('cerrar_credito');
            $nuevoSaldo = round(max(0, $saldoPendienteActual - $valorRecuperado), 2);

            $observacionTexto = trim((string) $request->input('observacion', ''));
            $credito->obse_cre = 'RECOJO DE MERCADERIA. ' . $observacionTexto
                . ' Fecha: ' . date('Y-m-d') . ' Usuario: ' . Auth::user()->name . ' Codigo Sede' . $idsede;

            if ($nuevoSaldo <= 0 || $cerrarManual) {
                // Lo recuperado cubre todo el saldo, o el staff decidió cerrar y condonar el resto: mismo cierre de siempre.
                foreach ($cuotasPendientes as $cuota) {
                    $cuota->esta_cuo = 'INCOBRABLE';
                    $cuota->save();
                }

                $credito->esta_cre = 2; // 2 = cerrado por recojo de mercadería, distinto de 0 = anulado
                $credito->save();

                $saldoIncobrable = $nuevoSaldo;
                $creditoCerrado = true;
            } else {
                // Queda saldo y no se pidió cerrar: se reemplazan las cuotas pendientes por un cronograma
                // nuevo sobre el saldo restante y el crédito sigue activo (mismo patrón que ReprogramacionCredito).
                $cantidadCuotas = $cuotasPendientes->count();
                $ultimaNumero = (int) Cuotas::where('credito_id', $credito->id)->max('numero_cuo');
                $montoBase = floor(($nuevoSaldo / $cantidadCuotas) * 100) / 100;

                foreach ($cuotasPendientes as $index => $cuota) {
                    $monto = ($index === $cantidadCuotas - 1)
                        ? round($nuevoSaldo - ($montoBase * ($cantidadCuotas - 1)), 2)
                        : $montoBase;

                    $nueva = new Cuotas;
                    $nueva->credito_id = $credito->id;
                    $nueva->fven_cuo = $cuota->fven_cuo;
                    $nueva->numero_cuo = $ultimaNumero + $index + 1;
                    $nueva->mont_cuo = $monto;
                    $nueva->saldo_cuo = $monto;
                    $nueva->capi_cuo = $monto;
                    $nueva->sald_cap = $monto;
                    $nueva->esta_cuo = 'PENDIENTE';
                    $nueva->version = 1;
                    $nueva->save();

                    $cuota->esta_cuo = 'REPROGRAMADA';
                    $cuota->saldo_cuo = 0;
                    $cuota->capi_cuo = 0;
                    $cuota->save();
                }

                // El crédito permanece esta_cre = 1 (activo) para permitir un futuro recojo parcial.
                $credito->save();

                $saldoIncobrable = 0;
                $creditoCerrado = false;
            }

            RecojoMercaderia::create([
                'credito_id' => $credito->id,
                'cliente_id' => $credito->cliente_id,
                'vendedor_recojo_id' => $request->vendedor_recojo_id,
                'user_id' => $user_id,
                'traslado_id' => $trasladoId,
                'sede_id' => $idsede,
                'fecha' => date('Y-m-d'),
                'saldo_incobrable' => $saldoIncobrable,
                'valor_recuperado' => $valorRecuperado,
                'credito_cerrado' => $creditoCerrado,
                'observacion' => $observacionTexto ?: null,
            ]);

            DB::commit();

            return redirect()->route('admin.recojo')->with('success', 'Recojo de mercadería registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar recojo de mercadería', [
                'credito_id' => $request->credito_id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'No se pudo procesar el recojo de mercadería. Intente nuevamente o contacte a soporte.');
        }
    }
}
