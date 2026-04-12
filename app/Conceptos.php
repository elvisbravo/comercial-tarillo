<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conceptos extends Model
{
    //

    protected $table='conceptos';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'descripcion','tipo_movimiento','estado'
    ];
}
