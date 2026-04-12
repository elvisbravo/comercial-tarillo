<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kardexes', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('producto_id')->unsigned();
            $table->bigInteger('almacen_id')->unsigned();

            $table->date('fecha');
            $table->string('descripcion');
            $table->integer('tipo');//1:ingreso, 2:salida
            $table->string('serie_comprobante',10);
            $table->string('correlativo_comprobante',10);
            $table->decimal('cantidad_unitaria',10,4);
            $table->decimal('precio_unitario',10,4);
            $table->decimal('subtotal_unitario',10,4);
            $table->decimal('cantidad_total',10,4);
            $table->decimal('precio_total',10,4);
            $table->decimal('subtotal_total',10,4);
            $table->string('tipo_envio',20);
            $table->boolean('estado');

            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('productos');
            //$table->foreign('almacen_id')->references('id')->on('almacenes');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kardexes');
    }
}
