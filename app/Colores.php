<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Colores extends Model
{
    //

    protected $table='colores';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'descripcion'
    ];
}
