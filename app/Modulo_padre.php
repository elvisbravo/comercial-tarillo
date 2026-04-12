<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Modulo_padre extends Model
{
    protected $table='modulo_padre';
    protected $primarykey='id';
    public $timestamps=true;

    protected $fillable = [
        'name', 'icon', 'order', 'state'
    ];
}