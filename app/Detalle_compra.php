<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_compra extends Model
{
    //
        protected $table='detalle_compras';
        protected $primarykey='id';
        public $timestamps=false;

        protected $fillable=['compra_id','producto_id','unidad_medida_id','cantidad',
        'precio','subtotal','igv','flete','costo','costo_flete'];
}
