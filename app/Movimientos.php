<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    //

    protected $table='movimientos';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'id_sesion_caja','forma_pago_id','concepto_id','fecha','hora','monto','descripcion','tipo_comprobante_id','moneda_id','descripcion_comprobante','tipo_envio','sede_id'
    ];
}
