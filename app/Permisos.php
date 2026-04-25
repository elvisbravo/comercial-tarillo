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
}
