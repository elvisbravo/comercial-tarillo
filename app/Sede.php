<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Sede extends Model
{
    //
    protected $table='sedes';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['empresa_id','nombre','direccion','telefono','estado','sede_principal','logo_sede','anexo','tipo_envio'];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

}
