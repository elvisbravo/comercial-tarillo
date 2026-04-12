<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unidad_medidas extends Model
{
    //

    protected $table='unidad_medidas';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'descripcion'
    ];
}
