<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Fix stock for product 43
    $stmt = $pdo->prepare("UPDATE detalle_almacen_productos SET stock = stock - 2 WHERE producto_id = 43 AND ubicacion_id = 13");
    $stmt->execute();
    echo "Stock corrected for product 43!\n";

} catch (PDOException $e) {
    echo $e->getMessage();
}
