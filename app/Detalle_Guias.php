<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_Guias extends Model
{
    //
    protected $table='_detalle_guia';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['producto_id','guia_detalle_id','cantidad','cantidad_recibido','diferencia','estado'];
}
