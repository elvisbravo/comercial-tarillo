<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forma_pago extends Model
{
    //

    protected $table='forma_pagos';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['descripcion','estado'];
}
