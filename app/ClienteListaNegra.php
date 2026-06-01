<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClienteListaNegra extends Model
{
    protected $table = 'cliente_lista_negra';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'cliente_id',
        'agregado_por',
        'quitado_por',
        'motivo',
        'notas',
        'agregado_en',
        'quitado_en',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'agregado_en' => 'datetime',
        'quitado_en' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function agregadoPor()
    {
        return $this->belongsTo(User::class, 'agregado_por');
    }

    public function quitadoPor()
    {
        return $this->belongsTo(User::class, 'quitado_por');
    }
}
