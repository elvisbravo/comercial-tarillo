<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    //
    protected $table='tipo_vehiculo';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['name','estado'];

}
