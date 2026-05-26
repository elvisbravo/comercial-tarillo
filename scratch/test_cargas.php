<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "=== RECENT CARGAS ===\n";
    $stmt = $pdo->query("SELECT t.id, t.fecha, t.hora, t.serie, t.correlativo, t.motivo, v.nombre as vendedor_nombre FROM traslados t LEFT JOIN vendedores v ON v.usuario_id = t.cliente_id WHERE t.serie = 'CAR' ORDER BY t.id DESC LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "No hay cargas registradas con serie = 'CAR'.\n";
    } else {
        foreach ($rows as $row) {
            echo "Carga ID: {$row['id']} - Serie: {$row['serie']}-{$row['correlativo']} - Fecha: {$row['fecha']} {$row['hora']} - Motivo: {$row['motivo']} - Vendedor: {$row['vendedor_nombre']}\n";
            
            // Detalle de la carga
            $stmt2 = $pdo->prepare("SELECT dt.producto_id, p.nomb_pro, dt.cantidad FROM detalle_traslado dt JOIN productos p ON p.id = dt.producto_id WHERE dt.traslado_id = ?");
            $stmt2->execute([$row['id']]);
            while ($det = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                echo "  -> Prod ID: {$det['producto_id']} - Nombre: {$det['nomb_pro']} - Cantidad: {$det['cantidad']}\n";
            }
        }
    }

    echo "\n=== VENDEDORES ===\n";
    $stmt3 = $pdo->query("SELECT id, nombre, estado, usuario_id, stock_location_id FROM vendedores");
    while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
        echo "Vendedor ID: {$row['id']} - Nombre: {$row['nombre']} - Estado: {$row['estado']} - Usuario ID: {$row['usuario_id']} - Ubicación ID: {$row['stock_location_id']}\n";
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}
