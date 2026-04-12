<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{


    protected $table='clientes';
    protected $primarykey='id';
    public $timestamps=true;


    protected $fillable = [
        'nomb_per','pate_per','mate_per','sexo_per','documento','dire_per','estado_per','anexo_concar','tipo_doc','usuario','telefono',
        'email','password','ubigeo_id','pais','usuario_registro','razon_social','usuario_modifico','codigo','id_sector','tipo_cliente','conyugue',
        'referencia'
    ];




}
