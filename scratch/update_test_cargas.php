<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "=== UPDATING CARGAS TO ASSIGN A TEST VENDEDOR ===\n";
    
    // We'll update the NULL cliente_ids to a valid vendedor ID (e.g., 42 RAFAEL ANTONIO GAVIRIA CASTILLO or 41 Jose Riva)
    $stmt = $pdo->prepare("UPDATE traslados SET cliente_id = 42 WHERE serie = 'CAR' AND cliente_id IS NULL");
    $stmt->execute();
    
    echo "Rows updated: " . $stmt->rowCount() . "\n";

} catch (PDOException $e) {
    echo $e->getMessage();
}
