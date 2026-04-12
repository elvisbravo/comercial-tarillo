<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    //
    
    protected $table='impuesto';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'impuesto','tipo_impuesto','etiqueta_factura','estado', 'empresa_id'
    ];
}
