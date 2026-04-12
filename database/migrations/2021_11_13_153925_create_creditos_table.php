<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->decimal('mont_cre',10,2);
            $table->string('esta_cre',10);
            $table->date('fech_cre');
            $table->decimal('inte_cre',10,4);
            $table->decimal('impo_cre',10,2);
            $table->date('fpag_cre',20)->nullable();
            $table->integer('peri_cre');
            $table->integer('cliente_id');
            $table->string('obse_cre')->nullable();
            $table->string('cheq_cre',50)->nullable();
            $table->string('reci_cre')->nullable();
            $table->string('usuario');
            $table->string('tipo_doc',5)->nullable();
            $table->integer('idaval')->nullable();
            $table->date('cancelacion')->nullable();
            $table->date('f_anulacion')->nullable();
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
        Schema::dropIfExists('creditos');
    }
}
