<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Kardex;
use Illuminate\Support\Facades\DB;

$producto_id = 30;

$items = DB::table('kardexes')
    ->select('producto_id', 'ubicacion_id', 'tipo_envio')
    ->where('producto_id', $producto_id)
    ->groupBy('producto_id', 'ubicacion_id', 'tipo_envio')
    ->get();

foreach ($items as $item) {
    echo "Recalculando Producto {$item->producto_id}, Ubicación {$item->ubicacion_id}, Envio {$item->tipo_envio}\n";
    
    $movimientos = Kardex::where('producto_id', $item->producto_id)
        ->where('ubicacion_id', $item->ubicacion_id)
        ->where('tipo_envio', $item->tipo_envio)
        ->orderBy('fecha', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    $running_cantidad = 0;
    $running_subtotal = 0;
    $current_avg_price = 0;

    foreach ($movimientos as $mov) {
        $tipo = (int)$mov->tipo;
        $original_cantidad = (float)$mov->cantidad_unitaria;
        $cantidad = $original_cantidad;

        // Lógica corregida según el usuario (ID 7)
        if ($mov->tipo_comprobante == 7) {
            $traslado = DB::table('traslados')
                ->where('serie', $mov->serie_comprobante)
                ->where('correlativo', $mov->correlativo_comprobante)
                ->first();

            if ($traslado) {
                $detalleTraslado = DB::table('detalle_traslado')
                    ->where('traslado_id', $traslado->id)
                    ->where('producto_id', $mov->producto_id)
                    ->first();

                if ($detalleTraslado && !empty($detalleTraslado->cantidad_recibido) && (float)$detalleTraslado->cantidad_recibido > 0) {
                    $cantidad = (float)$detalleTraslado->cantidad_recibido;
                    $mov->cantidad_unitaria = $cantidad;
                    if ($tipo === 1) {
                        $mov->subtotal_unitario = $cantidad * (float)$mov->precio_unitario;
                    }
                } else {
                    $cantidad = 0;
                    $mov->cantidad_unitaria = 0;
                    $mov->subtotal_unitario = 0;
                }
            } else {
                $cantidad = 0;
                $mov->cantidad_unitaria = 0;
                $mov->subtotal_unitario = 0;
            }
        } else if ($mov->tipo_comprobante == 6 || $mov->tipo_comprobante == 12) {
            $cantidad = 0;
            $mov->cantidad_unitaria = 0;
            $mov->subtotal_unitario = 0;
        }

        if ($tipo === 1) { // Ingreso
            $running_cantidad += $cantidad;
            $running_subtotal += (float)$mov->subtotal_unitario;
        } else if ($tipo === 2) { // Salida
            $mov->subtotal_unitario = $current_avg_price * $cantidad;
            $running_cantidad -= $cantidad;
            $running_subtotal -= (float)$mov->subtotal_unitario;
        }

        if ($running_cantidad <= 0) {
            $running_subtotal = 0;
            $current_avg_price = 0;
            $running_cantidad = ($running_cantidad < 0 && $tipo === 2) ? $running_cantidad : 0;
        } else {
            $current_avg_price = $running_subtotal / $running_cantidad;
        }

        $mov->cantidad_total = $running_cantidad;
        $mov->precio_total = $current_avg_price;
        $mov->subtotal_total = $running_subtotal;
        
        echo "ID: {$mov->id}, Tipo: {$tipo}, Cant: {$original_cantidad} -> {$cantidad}, Total: {$running_cantidad}\n";
        
        $mov->save();
    }
}
echo "Finalizado.\n";
