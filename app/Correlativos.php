<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Correlativos extends Model
{
    protected $table='correlativos';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['serie','correlativo','sede_id','tipo_comprobante_id','tipo_envio'];
}
