<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "=== SIMULATING VENDEDOR LOAD HISTORY FOR USER ID 67 ===\n";
    
    // Simulating vendedor query
    $stmt = $pdo->prepare("
        SELECT t.id, t.fecha, t.hora, t.serie, t.correlativo, t.motivo, t.estado, u.name as usuario_nombre 
        FROM traslados t 
        LEFT JOIN users u ON u.id = t.user_id 
        WHERE t.serie = 'CAR' 
          AND t.cliente_id = ? 
          AND t.sede_id = ? 
          AND t.tipo_envio = ?
        ORDER BY t.id DESC
    ");
    
    $stmt->execute([67, 11, 1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($rows)) {
        echo "No se encontraron cargas para este vendedor.\n";
    } else {
        foreach ($rows as $row) {
            echo "Carga ID: {$row['id']} - Serie: {$row['serie']}-{$row['correlativo']} - Fecha: {$row['fecha']} {$row['hora']} - Motivo: {$row['motivo']} - Autorizado por: {$row['usuario_nombre']}\n";
            
            // Detalle
            $stmt2 = $pdo->prepare("SELECT dt.producto_id, p.nomb_pro, dt.cantidad FROM detalle_traslado dt JOIN productos p ON p.id = dt.producto_id WHERE dt.traslado_id = ?");
            $stmt2->execute([$row['id']]);
            while ($det = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                echo "  -> Prod ID: {$det['producto_id']} - Nombre: {$det['nomb_pro']} - Cantidad: {$det['cantidad']}\n";
            }
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}
