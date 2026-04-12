<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table='almacenes';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['sede_id','nombre','direccion','estado','abreviatura'];
}
