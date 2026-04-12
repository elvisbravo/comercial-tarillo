<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Constantes;

class Modulo extends Model
{
    protected $table='modulo';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'name', 'url', 'order', 'state', 'idmodulo_padre'
    ];

    public function moduloPadre()
    {
        return $this->belongsTo(Modulo_padre::class, 'idmodulo_padre');
    }

    public function moduloFuncion()
    {
        return $this->hasMany(Funcion_Modulo::class, 'idmodulo')->where('state', Constantes::STATUS_ACTIVE);
    }
}