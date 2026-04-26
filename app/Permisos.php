<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Constantes;

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
}
