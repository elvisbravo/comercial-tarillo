<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Precios extends Model
{
    //
    protected $table='precios';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [

        'lista_id','articulo_id','sucursal_id','precio_contado','descuento_contado','precio_credito','descuento_credito','estado'
    ];

}
