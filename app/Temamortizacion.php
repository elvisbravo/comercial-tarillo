<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Temamortizacion extends Model
{
    //
    protected $table='temamortizacion';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['credito_id','cliente_id','monto','fecha'];
}
