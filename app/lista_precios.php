<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class lista_precios extends Model
{
    //
    protected $table='lista_precios';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'descripcion','vigencia','fecha_inicial','fecha_final','estado'
    ];

}
