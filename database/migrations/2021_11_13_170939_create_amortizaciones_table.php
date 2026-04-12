<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmortizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amortizaciones', function (Blueprint $table) {
            
            $table->id();
            $table->decimal('mont_amo',10,2);
            $table->date('fech_amo');
            $table->integer('cuota_id');
            $table->integer('recibo_id');
            $table->string('tipo_amo',50);
            $table->decimal('capi_amo',18,2);
            $table->decimal('inte_amo',18,2);
            $table->decimal('saldo_cuo',18,2);
            $table->timestamps();

            $table->foreign('cuota_id')->references('id')->on('cuotas');
            $table->foreign('recibo_id')->references('id')->on('recibos');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amortizaciones');
    }
}
