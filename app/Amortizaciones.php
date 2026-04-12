<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amortizaciones extends Model
{
    //

    protected $table='amortizaciones';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['mont_amo','fech_amo','cuota_id','recibo_id','tipo_amo','capi_amo','inte_amo','saldo_cuo'];

}
