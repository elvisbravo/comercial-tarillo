<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_comprobantes extends Model
{
    protected $table='tipo_comprobantes';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['descripcion','codigo_sunat','estado'];
}
