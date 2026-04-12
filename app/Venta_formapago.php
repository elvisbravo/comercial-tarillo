<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta_formapago extends Model
{

    protected $table = 'venta_formapago';
    protected $primarykey = 'id';
    public $timestamps = false;

    protected $fillable = ['venta_id', 'forma_pago_id','monto','numero_operacion','banco_id','movimiento_id'];

}
