<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DireEntrega extends Model
{
    //

    protected $table='dire_entrega';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'cliente_id','nombre_contacto','direccion','pais',
        'correo','telefono','ubigeo_id','usuario_registro','referencia',
        'usuario_modifico','estado','id_sector'
    ];

}
