<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tipos = DB::table('tipo_comprobantes')->get();
foreach ($tipos as $tipo) {
    echo "ID: {$tipo->id}, Desc: {$tipo->descripcion}\n";
}
