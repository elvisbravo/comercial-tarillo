<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Sede;
use App\Empresa;
use App\Almacen;
use App\Tipo_comprobantes;
use App\Correlativos;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');


    }


    public function index(Request $request)
    {
        $usuario = $request->session()->get('key');

        if ($usuario->sede_id == 1 || \App\User::find($usuario->id)->sede_id == 1) {

            // Resetear la sesión al usuario original desde BD para limpiar la sede elegida
            $usuarioOriginal = \App\User::find($usuario->id);
            $request->session()->put('key', $usuarioOriginal);

            $sedes = Sede::where('id', '!=', 1)->where('estado', 1)->get();
            $comprobantes = Tipo_comprobantes::all();

            return view('sedes.index', compact('sedes', 'comprobantes'));

        } else {

            return redirect()->route('home');
        }
    }

    /**
     * Actualiza la sede activa en sesión cuando el administrador selecciona una sede.
     */
    public function seleccionar_sede(Request $request)
    {
        $idsede  = $request->input('sede_id');
        $usuario = $request->session()->get('key');

        if (!$usuario) {
            return response()->json(['error' => 'Sin sesión activa'], 401);
        }

        // Solo permitir si el usuario es administrador (sede original = 1)
        if ($usuario->sede_id != 1) {
            return response()->json(['error' => 'Sin permiso'], 403);
        }

        $sede = Sede::find($idsede);
        if (!$sede) {
            return response()->json(['error' => 'Sede no encontrada'], 404);
        }

        // Clonar el usuario de sesión y cambiar la sede activa
        $usuarioClonado           = clone $usuario;
        $usuarioClonado->sede_id  = $sede->id;
        $usuarioClonado->sede     = $sede; // para que sedeDelUsuario funcione en el home
        $request->session()->put('key', $usuarioClonado);

        return response()->json(['ok' => true, 'sede' => $sede->nombre]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sedes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'anexo' => 'required',
            'principal' => 'required',
        ]);

        $empresa = Empresa::first();

        $sede = new Sede;
        $sede->nombre = $request->nombre;
        $sede->direccion = $request->direccion;
        $sede->telefono = $request->telefono;
        $sede->anexo = $request->anexo;
        $sede->sede_principal = $request->principal;
        $sede->estado = 1;
        $sede->logo_sede = "no hay";
        $sede->tipo_envio = 0;
        $sede->empresa_id = $empresa['id'];
        $sede->save();

        $consulta_idsede = Sede::orderBy('id','asc')->get();
        $ultimo_id_sede = $consulta_idsede->last();

        $almacen = new Almacen;

        $almacen->nombre = $request->nombre;
        $almacen->direccion = $request->direccion;
        $almacen->estado = 1;
        $almacen->sede_id = $ultimo_id_sede['id'];

        $almacen->save();

        return redirect()->route('sedes.index')
                        ->with('success','Sede created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sede  $sede
     * @return \Illuminate\Http\Response
     */
    public function show(Sede $sede)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sede  $sede
     * @return \Illuminate\Http\Response
     */
    public function edit(Sede $sede)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sede  $sede
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sede $sede)
    {
        //
    }

    public function update_envio(Request $request)
    {
        $sede= Sede::find($request->idsede);
        $sede->tipo_envio = $request->envio;

        $sede->save();

        return response()->json($request);
    }

    public function update_estado(Request $request)
    {
        $sede= Sede::find($request->idsede);
        $sede->estado = $request->estado;

        $sede->save();

        return response()->json($request);
    }

    public function correlativos($id)
    {
        $tipoEnvio = request()->get('tipo_envio');
        $query = DB::table('correlativos as co')
            ->select('co.id','co.serie','co.correlativo','co.sede_id','co.tipo_comprobante_id','co.tipo_envio','tc.descripcion')
            ->join('tipo_comprobantes as tc','co.tipo_comprobante_id','=','tc.id')
            ->where('co.sede_id','=',$id);

        if ($tipoEnvio !== null) {
            $query->where('co.tipo_envio', '=', $tipoEnvio);
        }

        $correlativos = $query->get();

        echo json_encode($correlativos);
    }

    public function select_comprobante(Request $request)
    {
        $query = Correlativos::where('sede_id','=',$request->idsede)->where('tipo_comprobante_id','=',$request->comprobante);

        if ($request->has('tipo_envio')) {
            $query->where('tipo_envio', '=', $request->tipo_envio);
        }

        $correlativos = $query->get();

        if (count($correlativos) === 0) {
            $comp = Tipo_comprobantes::find($request->comprobante);

            $json = array(
                "respuesta" => "ok",
                "mensaje" => "si puede agregar",
                "comprobante" => $comp['descripcion'],
                "idcomprobante" => $comp['id']
            );
        } else {
            $json = array(
                "respuesta" => "existe",
                "mensaje" => "ya existe dicho comprobante para este tipo de envío"
            );

        }

        return response()->json($json);

    }

    public function guardar_correlativos(Request $request)
    {
        $idsede = $request->idsede;

        // Obtener tipo_envio actual de la sede
        $sede = Sede::find($idsede);
        $tipoEnvio = $sede ? (int) $sede->tipo_envio : 0;

        // Reunir TODOS los comprobantes (nuevos + existentes)
        $nuevos = $request->tipocomprobante ?? [];       // name="tipocomprobante[]"
        $existentes = $request->tipocomprobantetraido ?? []; // name="tipocomprobantetraido[]"
        $series = $request->serie_traido ?? [];           // name="serie_traido[]"
        $correlativos = $request->correlativo_traido ?? []; // name="correlativo_traido[]"
        $idsCorrelativos = $request->correlativo_id ?? [];   // name="correlativo_id[]"

        // Combinar todos los comprobantes en orden: primero existentes, luego nuevos
        $todosComprobantes = array_merge($existentes, $nuevos);

        // Eliminar correlativos que ya no están en la lista
        if (!empty($idsCorrelativos)) {
            Correlativos::where('sede_id', $idsede)
                ->where('tipo_envio', $tipoEnvio)
                ->whereNotIn('id', $idsCorrelativos)
                ->delete();
        } else {
            // Si no hay IDs existentes, eliminar todos los de este tipo_envio
            Correlativos::where('sede_id', $idsede)
                ->where('tipo_envio', $tipoEnvio)
                ->delete();
        }

        foreach ($todosComprobantes as $i => $comprobanteId) {
            $serie = $series[$i] ?? '';
            $correlativoNum = $correlativos[$i] ?? '';

            if (empty($serie) || empty($correlativoNum)) continue;

            // Si ya existe un ID correlativo, actualizar; si no, crear nuevo
            if (isset($idsCorrelativos[$i])) {
                Correlativos::where('id', $idsCorrelativos[$i])
                    ->update([
                        'serie' => $serie,
                        'correlativo' => $correlativoNum,
                    ]);
            } else {
                Correlativos::create([
                    'serie' => $serie,
                    'correlativo' => $correlativoNum,
                    'sede_id' => $idsede,
                    'tipo_comprobante_id' => $comprobanteId,
                    'tipo_envio' => $tipoEnvio,
                ]);
            }
        }

        return response()->json("ok");
    }

    public function eliminar_correlativo($id)
    {
        $correlativo = Correlativos::find($id);
        if ($correlativo) {
            $correlativo->delete();
            return response()->json(['respuesta' => 'ok', 'mensaje' => 'Correlativo eliminado']);
        }
        return response()->json(['respuesta' => 'error', 'mensaje' => 'No encontrado']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sede  $sede
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sede $sede)
    {
        //
    }
}
