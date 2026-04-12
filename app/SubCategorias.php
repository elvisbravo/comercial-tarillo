<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategorias extends Model
{
    //

    protected $table='sub_categorias';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'subcategoria','estado','categoria_id'
    ];
}
