<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    //
    protected $table='vehiculo';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['placa','tipo_vehiculo_id','estado','num_soat','color','marca','modelo'];
}
