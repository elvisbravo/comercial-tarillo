<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_Guia_Factura extends Model
{
    //
    protected $table='facturas_guias';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['guia_id','factura_id'];
}
