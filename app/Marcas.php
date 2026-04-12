<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marcas extends Model
{
    //
        protected $table='marcas';
        protected $primarykey='id';
        public $timestamps=true;

        protected $fillable = [
            'descripcion'
        ];


}
