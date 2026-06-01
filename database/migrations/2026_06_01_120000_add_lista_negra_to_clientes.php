<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddListaNegraToClientes extends Migration
{
    public function up()
    {
        // Flag rápido en clientes
        Schema::table('clientes', function (Blueprint $table) {
            $table->boolean('lista_negra')->default(false)->index()->after('estado_per');
        });

        // Historial de inclusiones/exclusiones
        Schema::create('cliente_lista_negra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('agregado_por')->nullable(); // users.id
            $table->unsignedBigInteger('quitado_por')->nullable();  // users.id
            $table->text('motivo')->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('agregado_en')->nullable();
            $table->timestamp('quitado_en')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('agregado_por')->references('id')->on('users');
            $table->foreign('quitado_por')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('lista_negra');
        });
        Schema::dropIfExists('cliente_lista_negra');
    }
}
