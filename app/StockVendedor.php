<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockVendedor extends Model
{
    protected $table = 'stock_vendedor';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'vendedor_id',
        'producto_id',
        'traslado_id',
        'detalle_traslado_id',
        'cantidad_cargada',
        'cantidad_vendida',
        'cantidad_disponible',
        'sede_id',
        'tipo_envio',
        'fecha_carga',
        'estado',
        'fecha_reporte',
        'user_reporte_id'
    ];

    protected $casts = [
        'fecha_carga' => 'date',
        'fecha_reporte' => 'datetime',
        'cantidad_cargada' => 'integer',
        'cantidad_vendida' => 'integer',
        'cantidad_disponible' => 'integer',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id', 'id');
    }

    public function traslado()
    {
        return $this->belongsTo(Traslado::class, 'traslado_id', 'id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id', 'id');
    }
}