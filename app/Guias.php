<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guias extends Model
{
    //
    protected $table='guias';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['numero_guia','fecha_emision','documento_referencia','serie_referencia','numero_referencia','modalidad_traslado','peso_bruto',
                       'motivo','fecha_recibido','hora_recibido','direccion_partida','ubigeo_partida','direccion_llegada','ubigeo_llegada','tipo_traslado_id',
                       'tipo_envio','proveedor_id','transporte_id','vehiculo_id','usuario_id','id_ubicacion_destino','tipo_documento_id','sede_id','cliente_id','estado','bultos','fecha_traslado'];

}
