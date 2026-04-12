<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {

            $table->id();
            $table->string('nomb_per',100);
            $table->string('pate_per',100)->nullable();
            $table->string('mate_per',100)->nullable();
            $table->string('sexo_per',10)->nullable();
            $table->string('documento',15)->nullable();
            $table->string('dire_per',150)->nullable();
            $table->string('estado_per',20)->nullable();
            $table->string('anexo_concar',50)->nullable();
            $table->string('tipo_doc',10);
            $table->string('usuario',50);
            $table->string('telefono',12)->nullable();
            $table->string('email')->nullable();
            $table->integer('sector_id')->nullable();
            $table->timestamps();

            $table->foreign('sector_id')->references('id')->on('sectores');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}
