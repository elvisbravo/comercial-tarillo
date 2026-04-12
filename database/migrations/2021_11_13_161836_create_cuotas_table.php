<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->decimal('mont_cuo',10,2);
            $table->date('fven_cuo');
            $table->decimal('saldo_cuo',10,2);
            $table->decimal('capi_cuo',10,2);
            $table->integer('credito_id');
            $table->string('esta_cuo',15);
            $table->integer('numero_cuo');
            $table->decimal('sald_cap');
            $table->integer('version');
            $table->timestamps();

            $table->foreign('credito_id')->references('id')->on('creditos');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuotas');
    }
}
