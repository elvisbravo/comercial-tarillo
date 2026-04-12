<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('proveedor_id')->unsigned();
            $table->bigInteger('almacen_id')->unsigned();
            $table->bigInteger('moneda_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('forma_pago_id')->unsigned();
            $table->bigInteger('tipo_pago_id')->unsigned();
            $table->bigInteger('tipo_comprobante_id')->unsigned();
            //$table->bigInteger('movimiento_id')->unsigned();

            $table->date('fecha_ingreso');
            $table->date('fecha_compra');
            $table->string('serie_comprobante',10);
            $table->string('correlativo_comprobante',10);
            $table->decimal('compra_venta',10,4);
            $table->decimal('total_igv',10,4);
            $table->decimal('total_compra',10,4);
            $table->decimal('cambio_monto',10,4);
            $table->decimal('porcentaje_igv',10,2);

            $table->foreign('proveedor_id')->references('id')->on('proveedors');
            //$table->foreign('almacen_id')->references('id')->on('almacenes');
           // $table->foreign('moneda_id')->references('id')->on('monedas');
            $table->foreign('user_id')->references('id')->on('users');
            //$table->foreign('forma_pago_id')->references('id')->on('forma_pagos');
            //$table->foreign('tipo_pago_id')->references('id')->on('tipo_pagos');
            //$table->foreign('tipo_comprobante_id')->references('id')->on('tipo_comprobantes');

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
        Schema::dropIfExists('compras');
    }
}
