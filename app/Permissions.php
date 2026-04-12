<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    //

        protected $table='permissions';
        protected $primarykey='id';
        public $timestamps=true;

        protected $fillable = [
            'name', 'guard_name'
        ];
        

}
