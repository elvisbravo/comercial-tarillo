<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class candado extends Model
{
    //

    protected $table='candados';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'rango_minimo','rango_maximo','monto_inicial','nmeses','user_name','estado'
    ];


}


