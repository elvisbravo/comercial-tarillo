<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    //

    protected $table='proveedors';
        protected $primarykey='id';
        public $timestamps=true;

        protected $fillable = [
            'empresa_id','ruc','razon_social','nombre_comercial','telefono','direccion',
            'email','web_sitie','estado','contacto','documento_identidad'
        ];

}
