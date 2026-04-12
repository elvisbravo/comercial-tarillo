<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guias', function (Blueprint $table) {
            $table->id();
            $table->string('numero_guia',50)->unique();
            $table->date('fecha_emision');
            $table->integer('documento_referencia');
            $table->string('serie_referencia',50);
            $table->string('numero_referencia',50);
            $table->string('modalidad_traslado',50);
            $table->decimal('monto_total',10,3);
            $table->decimal('total_igv',10,3);
            $table->decimal('total_flete',10,3);
            $table->integer('cantidad_bienes');
            $table->decimal('peso_bruto',10,3);
            $table->string('motivo');
            $table->date('fecha_recibido');
            $table->time('hora_recibido');
            $table->string('direccion_partida');
            $table->string('ubigeo_partida');
            $table->string('direccion_llegada');
            $table->string('ubigeo_llegada');
            $table->integer('tipo_traslado_id');
            $table->integer('tipo_envio');
            $table->integer('moneda_id');
            $table->integer('proveedor_id');
            $table->integer('transporte_id');
            $table->integer('vehiculo_id');
            $table->integer('usuario_id');
            $table->integer('id_ubicacion_destino');
            $table->integer('tipo_documento_id');
            $table->integer('sede_id');
            $table->integer('cliente_id');
            $table->integer('estado');
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
        Schema::dropIfExists('guia');
    }
}
