<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    //
    protected $table='empresas';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'ubigeo_id','ruc','razon_social','nombre_comercial','direccion_fiscal','telefono','logo','usuario_sol','clave_sol','password_certificado'
    ];
}
