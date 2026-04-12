<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Creditos extends Model
{
    //

    protected $table='creditos';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'mont_cre','esta_cre','fech_cre','inte_cre','impo_cre','fpag_cre','peri_cre','cliente_id','obse_cre','cheq_cre',
        'reci_cre','usuario','tipo_doc','idaval','cancelacion','f_anulacion','id_venta','periodo_pago','sede_id','id_con'
        ,'codigo_garante'
    ];

    public function Detalle(){

        return $this->hasMany('App\Cuotas','credito_id','id')->orderBy('fven_cuo', 'asc');
    }



}
