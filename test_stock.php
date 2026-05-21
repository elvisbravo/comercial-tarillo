<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ventas = \App\Venta::where('venta_estado', 1)->orderBy('id', 'desc')->take(3)->get();
foreach($ventas as $v) {
    echo "Venta ID: " . $v->id . " - Fecha: " . $v->created_at . " - Estado Liq: " . $v->estado_liquidacion . PHP_EOL;
    $detalles = \App\Detalle_venta::where('venta_id', $v->id)->get();
    foreach($detalles as $d) {
        echo "  Prod: " . $d->producto_id . " Cant: " . $d->cantidad . " Ubicacion: " . $d->ubicacion_id . PHP_EOL;
        $stock = \DB::table('detalle_almacen_productos')->where('producto_id', $d->producto_id)->where('ubicacion_id', $d->ubicacion_id)->value('stock');
        echo "    Stock actual en bd para producto " . $d->producto_id . " en ubicacion " . $d->ubicacion_id . " es: " . $stock . PHP_EOL;
    }
}
