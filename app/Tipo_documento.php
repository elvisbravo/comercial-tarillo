<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_documento extends Model
{
    protected $table = 'tipo_documento';
    protected $primarykey = 'id';
    public $timestamps = true;

    protected $fillable = ['nombre','estado'];
}
