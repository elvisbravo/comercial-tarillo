<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendedorSector extends Model
{
    protected $table = 'vendedor_sector';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'vendedor_id', 'sector_id', 'fecha'
    ];

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class, 'vendedor_id', 'usuario_id');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id', 'id');
    }
}
