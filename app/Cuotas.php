<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuotas extends Model
{
    //

    protected $table='cuotas';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'mont_cuo','fven_cuo','saldo_cuo','capi_cuo','credito_id','esta_cuo','numero_cuo',
        'sald_cap','version'
    ];

}
