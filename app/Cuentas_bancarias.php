<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuentas_bancarias extends Model
{
    protected $table='cuentas_bancarias';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['cuenta_corriente','cuenta_cci','banco_id','estado'];
}
