<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StokLocation extends Model
{
    //
    protected $table='stock_location';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['name','almacen_id','responsable','type_location','es_chatarra','devolucion','estado'];
}
