<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_traslado extends Model
{
    protected $table='detalle_traslado';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['producto_id','traslado_id','cantidad','cantidad_recibido','diferencia','estado'];
}
