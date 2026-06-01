<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdLocalToVentas extends Migration
{
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('id_local', 36)->nullable()->unique()->after('id');
            $table->timestamp('fecha_offline_created')->nullable()->after('id_local');
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropUnique(['id_local']);
            $table->dropColumn(['id_local', 'fecha_offline_created']);
        });
    }
}
