<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
use Illuminate\Support\Facades\DB;

try {
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $producto_id = 30;
    
    $kardex = DB::table('kardexes')
        ->where('producto_id', $producto_id)
        ->orderBy('fecha', 'asc')
        ->orderBy('id', 'asc')
        ->get();
        
    echo "ID | Tipo | Comp | Serie-Corr | Cant | Total | Subtotal\n";
    foreach ($kardex as $k) {
        echo sprintf("%d | %d | %d | %s-%s | %.2f | %.2f | %.2f\n", 
            $k->id, $k->tipo, $k->tipo_comprobante, $k->serie_comprobante, $k->correlativo_comprobante, 
            $k->cantidad_unitaria, $k->cantidad_total, $k->subtotal_total);
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
