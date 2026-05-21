<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "=== RECENT SALES ===" . PHP_EOL;
    $stmt = $pdo->query("SELECT id, monto, tipo_pago_id, estado_liquidacion, created_at, tipo_comprobante_id FROM ventas ORDER BY id DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Venta ID: {$row['id']} - Comprobante: {$row['tipo_comprobante_id']} - Estado Liq: {$row['estado_liquidacion']} - Fecha: {$row['created_at']}\n";
        
        $stmt2 = $pdo->prepare("SELECT producto_id, cantidad, ubicacion_id FROM detalle_venta WHERE venta_id = ?");
        $stmt2->execute([$row['id']]);
        while ($det = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            echo "  Prod: {$det['producto_id']} - Cantidad: {$det['cantidad']} - Ubicacion: {$det['ubicacion_id']}\n";
            $stmt3 = $pdo->prepare("SELECT stock, tipo_envio FROM detalle_almacen_productos WHERE producto_id = ? AND ubicacion_id = ?");
            $stmt3->execute([$det['producto_id'], $det['ubicacion_id']]);
            $stockRows = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            foreach ($stockRows as $sr) {
                echo "    Stock (tipo_envio: {$sr['tipo_envio']}): {$sr['stock']}\n";
            }
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
