<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('ubigeo_id')->unsigned();

            $table->string('ruc',11)->unique();
            $table->string('razon_social');
            $table->string('nombre_comercial');
            $table->string('direccion_fiscal');
            $table->string('telefono');
            $table->string('logo');
            $table->string('usuario_sol');
            $table->string('clave_sol');
            $table->string('password_certificado');

            $table->timestamps();

            //$table->foreign('ubigeo_id')->references('id')->on('ubigeos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}
