<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    //
    protected $table='compras';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['proveedor_id','ubicacion_id','moneda_id','user_id','forma_pago_id','tipo_pago_id','tipo_comprobante_id','fecha_ingreso','fecha_compra',
                         'serie_comprobante','correlativo_comprobante','compra_venta','total_igv','total_compra','cambio_monto','porcentaje_igv','perseccion','icbper','total_compra_flete','tipo_envio','estado'];
}
