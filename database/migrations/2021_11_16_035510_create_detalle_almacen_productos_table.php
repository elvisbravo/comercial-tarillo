<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleAlmacenProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_almacen_productos', function (Blueprint $table) {
            $table->id();
            $table->integer('stock');
            $table->string('tipo_envio');
            $table->integer('almacen_id');
            $table->integer('producto_id');
            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('productos');
            //$table->foreign('almacen_id')->references('id')->on('almacenes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_almacen_productos');
    }
}
