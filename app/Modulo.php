<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Constantes;

class Modulo extends Model
{
    protected $table = 'modulo';
    protected $primarykey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'url',
        'icon',
        'order',
        'state',
        'padre_id'
    ];

    public function submodulos()
    {
        return $this->hasMany(Modulo::class, 'padre_id')->where('state', true)->orderBy('order', 'asc');
    }
}
