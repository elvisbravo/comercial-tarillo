<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedors', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('empresa_id')->unsigned();

            $table->string('ruc')->unique();
            $table->string('razon_social');
            $table->string('nombre_comercial');
            $table->string('telefono');
            $table->string('direccion');
            $table->string('email');
            $table->string('web_sitie');
            $table->boolean('estado');
            $table->string('contacto');

            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proveedors');
    }
}
