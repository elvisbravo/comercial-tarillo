<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockVendedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_vendedor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendedor_id'); // users.id
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('traslado_id');
            $table->unsignedBigInteger('detalle_traslado_id')->nullable();
            $table->integer('cantidad_cargada')->default(0);
            $table->integer('cantidad_vendida')->default(0);
            $table->integer('cantidad_disponible')->default(0);
            $table->unsignedBigInteger('sede_id');
            $table->integer('tipo_envio')->default(1);
            $table->date('fecha_carga');
            $table->integer('estado')->default(1); // 0=anulado, 1=activo, 2=reportado
            $table->dateTime('fecha_reporte')->nullable();
            $table->unsignedBigInteger('user_reporte_id')->nullable();
            $table->timestamps();

            $table->foreign('vendedor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('traslado_id')->references('id')->on('traslados')->onDelete('cascade');
            $table->foreign('sede_id')->references('id')->on('sedes')->onDelete('cascade');

            $table->index(['vendedor_id', 'producto_id', 'fecha_carga', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_vendedor');
    }
}