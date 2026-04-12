<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Funcion_Modulo extends Model
{
    protected $table='funcion_modulo';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'idmodulo', 'idfuncion', 'state'
    ];
}