<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table='kardexes';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['producto_id','ubicacion_id','fecha','descripcion','tipo','serie_comprobante','correlativo_comprobante','cantidad_unitaria','precio_unitario','subtotal_unitario','cantidad_total','precio_total','subtotal_total','tipo_envio','estado'];
}
