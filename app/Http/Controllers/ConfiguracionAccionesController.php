<?php

namespace App\Http\Controllers;

use App\Modulo;
use App\Acciones;
use App\ModulosAcciones;
use Illuminate\Http\Request;

class ConfiguracionAccionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Obtener módulos padres
        $parents = Modulo::where('padre_id', 0)->where('state', true)->orderBy('order')->get();
        // Obtener submódulos
        $submodules = Modulo::where('padre_id', '>', 0)->where('state', true)->orderBy('order')->get();
        // Obtener todas las acciones disponibles
        $acciones = Acciones::where('estado', true)->get();
        
        return view('configuracion_acciones.index', compact('parents', 'submodules', 'acciones'));
    }

    public function getAssignments($modulo_id)
    {
        $assignments = ModulosAcciones::where('modulo_id', $modulo_id)->pluck('accion_id');
        return response()->json($assignments);
    }

    public function saveAssignments(Request $request)
    {
        $this->validate($request, [
            'modulo_id' => 'required',
            'acciones' => 'array'
        ]);

        // Eliminar asignaciones previas
        ModulosAcciones::where('modulo_id', $request->modulo_id)->delete();

        // Crear nuevas asignaciones
        if ($request->has('acciones')) {
            foreach ($request->acciones as $accion_id) {
                ModulosAcciones::create([
                    'modulo_id' => $request->modulo_id,
                    'accion_id' => $accion_id
                ]);
            }
        }

        return response()->json('OK');
    }
}
