<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Constantes;
use App\Modulo;
use App\Acciones;

class Permisos extends Model
{
    protected $table = 'permisos';
    protected $primarykey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'rol_id',
        'modulo_id',
        'accion_id',
        'sede_id'
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function accion()
    {
        return $this->belongsTo(Acciones::class, 'accion_id');
    }

    /**
     * Verifica si el usuario autenticado tiene un permiso específico para un módulo (por URL) y acción.
     */
    public static function hasPermission($modulo_url, $accion_id)
    {
        $user = auth()->user();
        if (!$user) return false;

        // Bypass para Administrador (por nombre de rol)
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        $idsede = session('key') ? session('key')->sede_id : null;
        if (!$idsede) return false;

        $modulo = Modulo::where('url', $modulo_url)->first();
        if (!$modulo) return false;

        // El sistema usa IDs de roles de Spatie en la tabla permisos
        $roleIds = $user->roles->pluck('id')->toArray();

        return self::whereIn('rol_id', $roleIds)
            ->where('modulo_id', $modulo->id)
            ->where('accion_id', $accion_id)
            ->where('sede_id', $idsede)
            ->exists();
    }

    /**
     * Obtiene los módulos y submódulos permitidos para el menú.
     */
    public static function getMenu()
    {
        $user = auth()->user();
        if (!$user) return [];

        // Bypass para Administrador
        $isAdmin = $user->hasRole('ADMINISTRADOR');

        $idsede = session('key') ? session('key')->sede_id : null;
        // Si no es admin y no hay sede, no hay menú (excepto si es admin que puede ver todo para configurar)
        if (!$idsede && !$isAdmin) return [];

        $roleIds = $user->roles->pluck('id')->toArray();

        // Buscar el ID de la acción "Ver" dinámicamente
        $accion_ver = Acciones::where('nombre', 'ILIKE', 'Ver')->first();
        $idAccionVer = $accion_ver ? $accion_ver->id : 1;

        // IDs de módulos con permiso de ver
        $moduloIdsPermitidos = [];
        if (!$isAdmin) {
            $moduloIdsPermitidos = self::whereIn('rol_id', $roleIds)
                ->where('accion_id', $idAccionVer)
                ->where('sede_id', $idsede)
                ->pluck('modulo_id')
                ->toArray();
        }

        // Módulos padres (padre_id = 0)
        $padres = Modulo::where('padre_id', 0)
            ->where('state', true)
            ->orderBy('order', 'asc')
            ->get();

        $menu = [];

        foreach ($padres as $padre) {
            // Submódulos permitidos
            $querySub = Modulo::where('padre_id', $padre->id)
                ->where('state', true)
                ->orderBy('order', 'asc');
            
            if (!$isAdmin) {
                $querySub->whereIn('id', $moduloIdsPermitidos);
            }
            
            $submodulos = $querySub->get();

            // Incluir si es admin OR si el padre está permitido OR si tiene hijos permitidos
            if ($isAdmin || in_array($padre->id, $moduloIdsPermitidos) || $submodulos->count() > 0) {
                $menu[] = (object)[
                    'id' => $padre->id,
                    'name' => $padre->name,
                    'url' => $padre->url,
                    'icon' => $padre->icon,
                    'submodulos' => $submodulos
                ];
            }
        }

        return $menu;
    }
}
