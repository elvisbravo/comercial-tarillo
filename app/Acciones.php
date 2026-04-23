<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Constantes;

class Acciones extends Model
{
    protected $table = 'acciones';
    protected $primarykey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado'
    ];
}
