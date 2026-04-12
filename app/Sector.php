<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    //

    protected $table='sectores';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'nomb_sec','estado'
    ];




}
