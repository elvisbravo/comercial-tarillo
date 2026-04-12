<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Funcion extends Model
{
    protected $table='funcion';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'name', 'function', 'icon', 'class', 'button', 'order', 'state'
    ];
}