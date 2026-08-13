<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecojoMercaderia extends Model
{
    protected $table = 'recojos_mercaderia';
    protected $primarykey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'credito_id',
        'cliente_id',
        'vendedor_recojo_id',
        'user_id',
        'traslado_id',
        'sede_id',
        'fecha',
        'saldo_incobrable',
        'observacion',
    ];

    public function credito()
    {
        return $this->belongsTo(Creditos::class, 'credito_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function vendedorRecojo()
    {
        return $this->belongsTo(User::class, 'vendedor_recojo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function traslado()
    {
        return $this->belongsTo(Traslado::class, 'traslado_id');
    }
}
