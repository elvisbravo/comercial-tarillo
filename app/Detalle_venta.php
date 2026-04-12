<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_venta extends Model
{

    protected $table = 'detalle_venta';
    protected $primarykey = 'id';
    public $timestamps = true;

    protected $fillable = ['producto_id','venta_id','cantidad','precio','created_at','updated_at', 'costo', 'subtotal', 'descripcion','ubicacion_id'];

}
