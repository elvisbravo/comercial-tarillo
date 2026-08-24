<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'zonas';
    protected $primarykey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nomb_zona', 'estado', 'sede_id'
    ];

    public function sectores()
    {
        return $this->hasMany(Sector::class, 'zona_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

}
