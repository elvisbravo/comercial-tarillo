<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleGuiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_detalle_guia', function (Blueprint $table) {
            $table->id();
            $table->integer('guia_detalle_id');
            $table->integer('producto_id');
            $table->integer('cantidad');
            $table->integer('cantidad_recibido');
            $table->integer('diferencia');
            $table->integer('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_detalle_guia');
    }
}
