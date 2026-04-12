<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nomb_pro',100);
            $table->decimal('prec_compra',10,4);
            $table->decimal('prec_ven',10,4);
            $table->string('cuenta_debe',20);
            $table->string('cuenta_haber',20);
            $table->string('controlstock',4)->nullable();
            $table->string('codigo_barras');
            $table->string('img');
            $table->integer('modelo_id')->nullable();
            $table->integer('unidad_medida_id')->nullable();
            $table->integer('marca_id')->nullable();
            $table->integer('color_id')->nullable();
            $table->integer('magnitud_id')->nullable();
            $table->timestamps();

            $table->foreign('modelo_id')->references('id')->on('modelos');
            $table->foreign('unidad_medida_id')->references('id')->on('unidad_medidas');
            $table->foreign('marca_id')->references('id')->on('marcas');
            $table->foreign('color_id')->references('id')->on('colores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
