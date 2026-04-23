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
        'accion_id'
    ];
}
