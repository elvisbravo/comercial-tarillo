<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DireccionesImagenes extends Model
{
    //

    protected $table='direcciones_imagenes';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable = [
        'path_image','status', 'dire_id'
    ];

}