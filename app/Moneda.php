<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    //

    protected $table='monedas';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'descripcion','simbolo','estado'
    ];
    
}
