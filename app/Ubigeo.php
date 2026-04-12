<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    protected $table='ubigeos';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['codigo_ubigeo','departamento','provincia','distrito'];
}
