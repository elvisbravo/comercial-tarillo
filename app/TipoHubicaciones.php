<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoHubicaciones extends Model
{
    //
    protected $table='_tipo_hubicaciones';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['name'];
}
