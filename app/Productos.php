<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    //

    protected $table='productos';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'nomb_pro','cuenta_debe','cuenta_haber','codigo_barras',
        'img','modelo_id','unidad_medida_id','marca_id',
        'color_id','categoria_id','subcategoria_id','estado','stock_minimo',
        'usuario_registro','usuario_modifico','costo','impuesto_id','peso','volumuen'
    ];
}
