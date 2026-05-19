<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    //
    protected $table='vendedores';
    protected $primarykey='id';
    public $timestamps=false;

    protected $fillable=['nombre','documento','direccion','usuario_id','estado','stock_location_id'];

    public function stockLocation()
    {
        return $this->belongsTo(StokLocation::class, 'stock_location_id', 'id');
    }

    public function sectores()
    {
        return $this->belongsToMany(Sector::class, 'vendedor_sector', 'vendedor_id', 'sector_id')
                    ->withPivot('fecha')
                    ->withTimestamps();
    }
}

