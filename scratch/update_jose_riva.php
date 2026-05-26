<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "=== UPDATING TEST CARGAS TO USER ID 67 (JOSE RIVA) ===\n";
    
    // Jose Riva has usuario_id = 67.
    // We update all CAR series traslados to have cliente_id = 67.
    $stmt = $pdo->prepare("UPDATE traslados SET cliente_id = 67 WHERE serie = 'CAR'");
    $stmt->execute();
    
    echo "Rows updated: " . $stmt->rowCount() . "\n";

} catch (PDOException $e) {
    echo $e->getMessage();
}
