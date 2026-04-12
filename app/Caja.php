<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    //
    protected $table='caja';
    protected $primaryKey = 'id';
    public $timestamps=false;

    protected $fillable=['sede_id','name_usuario','fecha_apertura','hora_apertura','fecha_cierre','hora_cierre','monto_apertura','monto_cierre_fisico','estado','user_id','monto_cierre_virtual'];

}
