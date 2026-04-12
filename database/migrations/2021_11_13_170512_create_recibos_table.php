<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecibosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();
            $table->decimal('mont_rec');
            $table->date('fech_rec');
            $table->integer('cliente_id');
            $table->string('fpag_rec',40);
            $table->text('obse_rec');
            $table->string('esta_rec',15);
            $table->string('docu_ref',100)->nullable();
            $table->string('insercion',20)->nullable();
            $table->string('usuario',100)->nullable();
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
        Schema::dropIfExists('recibos');
    }
}
