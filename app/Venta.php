<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{

    protected $table = 'ventas';
    protected $primarykey = 'id';
    public $timestamps = true;

    protected $fillable = ['moneda_id','tipo_comprobante_id','tipo_pago_id','user_id','fecha','hora','serie_comprobante','numero_comprobante','monto','sede_id','venta_estado','monto_entregado','vuelto','igv_monto','monto_sin_igv','fecha_eliminacion','user_eliminacion','aceptado_sunat','mensaje_sunat','tipo_envio','cod_sunat','hash_cdr','hash_cpe','cliente_id','descuento','vendedor_id','serie_nota_credito','numero_nota_credito','estado_nota'];

}
