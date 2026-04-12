<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $table='traslados';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['fecha','serie','correlativo','almacen_origen','almacen_destino','motivo','estado','tipo_envio','sede_id','cliente_id','conductor_id','vehiculo_id','peso_bruto','bultos',
                        'direccion_partida','ubigeo_partida','direccion_llegada','ubigeo_llegada','documento_referencia','serie_referencia','numero_referencia','modalidad_traslado','id_ubicacion_destino',
                        'tipo_traslado_id','email','id_documento_electronico','user_id','hora','fecha_recibido','hora_recibido','sede_destino','user_recepcion','id_ubicacion_origen'];
}
