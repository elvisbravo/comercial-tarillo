<?php

namespace App\Http\Controllers;

use App\ClienteListaNegra;
use App\Clientes;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListaNegraController extends Controller
{
    /**
     * Vista principal: lista actual + sugeridos.
     */
    public function index(Request $request)
    {
        $idsede = session('key')->sede_id;

        // 1) Clientes actualmente en lista negra
        $enListaNegra = DB::table('cliente_lista_negra as cln')
            ->join('clientes as c', 'c.id', '=', 'cln.cliente_id')
            ->leftJoin('users as u', 'u.id', '=', 'cln.agregado_por')
            ->select(
                'cln.id as registro_id',
                'c.id as cliente_id',
                'c.razon_social',
                'c.nomb_per',
                'c.pate_per',
                'c.mate_per',
                'c.documento',
                'c.telefono',
                'cln.motivo',
                'cln.notas',
                'cln.agregado_en',
                'u.name as agregado_por_nombre'
            )
            ->where('cln.activo', 1)
            ->orderBy('cln.agregado_en', 'desc')
            ->get();

        // 2) Clientes sugeridos (con créditos activos y cuotas vencidas)
        $sugeridos = $this->calcularSugeridos($idsede);

        return view('lista_negra.index', compact('enListaNegra', 'sugeridos'));
    }

    /**
     * Agrega un cliente a la lista negra.
     */
    public function agregar(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'motivo'     => 'required|string|max:500',
        ]);

        $cliente = Clientes::find($request->cliente_id);

        // Si ya está activo, no duplicar
        $yaActivo = ClienteListaNegra::where('cliente_id', $cliente->id)
            ->where('activo', 1)
            ->exists();

        if ($yaActivo) {
            return redirect()->back()->with('error', 'El cliente ya se encuentra en la lista negra.');
        }

        DB::beginTransaction();
        try {
            // Marcar el flag rápido
            $cliente->lista_negra = true;
            $cliente->save();

            // Crear el registro histórico
            ClienteListaNegra::create([
                'cliente_id'   => $cliente->id,
                'agregado_por' => Auth::id(),
                'motivo'       => $request->motivo,
                'notas'        => $request->notas,
                'agregado_en'  => now(),
                'activo'       => true,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Cliente agregado a la lista negra.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al agregar: ' . $e->getMessage());
        }
    }

    /**
     * Quita un cliente de la lista negra.
     */
    public function quitar(Request $request, $registroId)
    {
        $registro = ClienteListaNegra::findOrFail($registroId);
        if (!$registro->activo) {
            return redirect()->back()->with('error', 'Este registro ya no está activo.');
        }

        $request->validate([
            'motivo_salida' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $registro->activo      = false;
            $registro->quitado_por = Auth::id();
            $registro->quitado_en  = now();
            $registro->notas       = trim(($registro->notas ?? '') . "\n[Salida] " . $request->motivo_salida);
            $registro->save();

            // Verificar si tiene OTROS registros activos; si no, bajar el flag
            $otrosActivos = ClienteListaNegra::where('cliente_id', $registro->cliente_id)
                ->where('activo', 1)
                ->where('id', '!=', $registro->id)
                ->exists();
            if (!$otrosActivos) {
                $cliente = Clientes::find($registro->cliente_id);
                if ($cliente) {
                    $cliente->lista_negra = false;
                    $cliente->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Cliente removido de la lista negra.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al quitar: ' . $e->getMessage());
        }
    }

    /**
     * Búsqueda manual de clientes para agregar a la lista negra.
     */
    public function buscarClientes(Request $request)
    {
        $termino = trim($request->get('q', ''));
        $idsede  = session('key')->sede_id;

        $query = Clientes::query()
            ->where('lista_negra', false)
            ->where(function($q) use ($termino) {
                $q->where('razon_social', 'like', "%{$termino}%")
                  ->orWhere('nomb_per', 'like', "%{$termino}%")
                  ->orWhere('pate_per', 'like', "%{$termino}%")
                  ->orWhere('mate_per', 'like', "%{$termino}%")
                  ->orWhere('documento', 'like', "%{$termino}%");
            })
            ->select('id', 'razon_social', 'nomb_per', 'pate_per', 'mate_per', 'documento', 'telefono')
            ->orderBy('razon_social')
            ->limit(20);

        return response()->json($query->get());
    }

    /**
     * Lógica de sugerencia: clientes con créditos activos y al menos 1 cuota vencida.
     * (excluye los que ya están en lista negra)
     */
    private function calcularSugeridos($idsede = null)
    {
        $hoy = date('Y-m-d');

        $query = DB::table('clientes as c')
            ->join('creditos as cr', 'cr.cliente_id', '=', 'c.id')
            ->join('cuotas as cu', 'cu.credito_id', '=', 'cr.id')
            ->leftJoin('users as v', 'v.id', '=', DB::raw('(SELECT vendedor_id FROM ventas WHERE id = cr.id_venta LIMIT 1)'))
            ->where('cr.esta_cre', '1')                          // Solo créditos activos
            ->where('cu.esta_cuo', 'PENDIENTE')                  // Cuotas pendientes
            ->where('cu.fven_cuo', '<', $hoy)                    // Vencidas
            ->where('c.lista_negra', 0)                          // Que NO estén ya en la lista
            ->whereNull('cr.f_anulacion')                        // Crédito no anulado
            ->groupBy('c.id', 'c.razon_social', 'c.nomb_per', 'c.pate_per', 'c.mate_per', 'c.documento', 'c.telefono', 'v.name')
            ->select(
                'c.id as cliente_id',
                'c.razon_social',
                'c.nomb_per',
                'c.pate_per',
                'c.mate_per',
                'c.documento',
                'c.telefono',
                'v.name as ultimo_vendedor',
                DB::raw('COUNT(DISTINCT cu.id) as cuotas_vencidas'),
                DB::raw('COUNT(DISTINCT cr.id) as creditos_activos'),
                DB::raw('SUM(cu.saldo_cuo) as deuda_total'),
                DB::raw('MAX(CURRENT_DATE - cu.fven_cuo) as max_dias_atraso')
            )
            ->orderByDesc('cuotas_vencidas')
            ->orderByDesc('max_dias_atraso');

        return $query->get();
    }
}
