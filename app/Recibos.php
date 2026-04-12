<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recibos extends Model
{
    //
    protected $table='recibos';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable=['mont_rec','fech_rec','cliente_id','fpag_rec','obse_rec','esta_rec','docu_ref','insercion','usuario',
                          'f_anulacion','num_recibo','id_movimiento','sede_id','usuario_anulo','vendedor_id'];

    public function Amortizaciones() {

        return $this->hasMany('App\Amortizaciones','recibo_id','id');

    }

}
