<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaFoto extends Model
{
    protected $table = 'venta_fotos';
    protected $fillable = ['venta_id', 'foto_path', 'tipo_foto'];
    public $timestamps = false;

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
