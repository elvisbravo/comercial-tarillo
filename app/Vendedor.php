<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    //
    protected $table='vendedores';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['nombre','documento','direccion','usuario_id','estado'];
}
