<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasGuiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas_guias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guia_id');
            $table->unsignedBigInteger('factura_id');
            $table->timestamps();

            $table->foreign('guia_id')->references('id')->on('guias')->onDelete('cascade');
            $table->foreign('factura_id')->references('id')->on('compras')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facturas_guias');
    }
}
