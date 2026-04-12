<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concepto_credito extends Model
{
    //

    protected $table='concepto_credito';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'name','estado'
    ];


}
