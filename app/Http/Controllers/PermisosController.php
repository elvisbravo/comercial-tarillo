<?php

namespace App\Http\Controllers;

use App\Modulo;
use App\Acciones;
use App\ModulosAcciones;
use App\Permisos;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class PermisosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = Role::all();
        // Obtener módulos padres y sub con sus acciones ya mapeadas
        $parents = Modulo::where('padre_id', 0)->where('state', true)->orderBy('order')->get();
        $submodules = Modulo::where('padre_id', '>', 0)->where('state', true)->orderBy('order')->get();
        
        // Cargar acciones configuradas para cada submódulo
        foreach ($submodules as $sub) {
            $sub->acciones_configuradas = ModulosAcciones::where('modulo_id', $sub->id)
                ->join('acciones', 'modulos_acciones.accion_id', '=', 'acciones.id')
                ->select('acciones.*')
                ->get();
        }

        // Buscar la acción "Ver" o crearla si no existe (id sugerido 1)
        $accion_ver = Acciones::where('nombre', 'ILIKE', 'Ver')->first();
        if (!$accion_ver) {
            // Si no existe, podemos usar un ID 0 ficticio o buscar una genérica
            // Por ahora asumiremos que existe o es el identificador de acceso al módulo
        }

        return view('permisos.index', compact('roles', 'parents', 'submodules', 'accion_ver'));
    }

    public function getPermissionsByRole($rol_id)
    {
        $permisos = Permisos::where('rol_id', $rol_id)->get();
        return response()->json($permisos);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'rol_id' => 'required',
            'permisos' => 'array'
        ]);

        // Transacción manual: eliminar previos del rol
        Permisos::where('rol_id', $request->rol_id)->delete();

        if ($request->has('permisos')) {
            foreach ($request->permisos as $p) {
                Permisos::create([
                    'rol_id' => $request->rol_id,
                    'modulo_id' => $p['modulo_id'],
                    'accion_id' => $p['accion_id']
                ]);
            }
        }

        return response()->json('OK');
    }
}
