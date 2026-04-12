<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transportista extends Model
{
    //
    
    protected $table='_transportista';
        protected $primarykey='id';
        public $timestamps=true;

        protected $fillable = [
            'ruc','razon_social','nombre_comercial','telefono','direccion', 'estado', 'documento_identidad'
        ];
}
