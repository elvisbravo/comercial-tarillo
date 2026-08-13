<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'zonas';
    protected $primarykey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nomb_zona', 'estado'
    ];

    public function sectores()
    {
        return $this->hasMany(Sector::class, 'zona_id');
    }

}
