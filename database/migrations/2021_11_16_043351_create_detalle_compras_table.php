<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('compra_id')->unsigned();
            $table->bigInteger('producto_id')->unsigned();
            $table->bigInteger('unidad_medida_id')->unsigned();

            $table->integer('cantidad');
            $table->decimal('precio',10,4);
            $table->decimal('subtotal',10,4);
            $table->decimal('igv',10,4);

            $table->timestamps();

            $table->foreign('compra_id')->references('id')->on('compras');
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->foreign('unidad_medida_id')->references('id')->on('unidad_medidas');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_compras');
    }
}
