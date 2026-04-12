<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('empresa_id')->unsigned();

            $table->string('nombre');
            $table->string('direccion');
            $table->string('telefono');
            $table->boolean('estado');
            $table->integer('sede_principal');
            $table->string('logo_sede');
            $table->string('anexo');
            $table->string('tipo_envio');

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
        Schema::dropIfExists('sedes');
    }
}
