<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_pago extends Model
{
    //

    protected $table='tipo_pagos';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['descripcion','estado'];
}
