<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    //

    protected $table='conductor';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'nombre','estado','numero_documento','categoria_licencia','num_licencia'
    ];
}
