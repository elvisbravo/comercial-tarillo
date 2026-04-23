<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Constantes;

class ModulosAcciones extends Model
{
    protected $table = 'modulos_acciones';
    protected $primarykey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'modulo_id',
        'accion_id'
    ];
}
