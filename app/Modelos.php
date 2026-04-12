<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modelos extends Model
{
    //
    protected $table='modelos';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'descripcion'
    ];
}
