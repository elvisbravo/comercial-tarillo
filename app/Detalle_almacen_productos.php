<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_almacen_productos extends Model
{
    //
    protected $table='detalle_almacen_productos';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['stock','tipo_envio','producto_id','estado','ubicacion_id'];



}
