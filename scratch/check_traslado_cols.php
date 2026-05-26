<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "=== COLUMNS FOR ID 298 ===\n";
    $stmt = $pdo->prepare("SELECT * FROM traslados WHERE id = ?");
    $stmt->execute([298]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    foreach ($row as $col => $val) {
        echo "$col => $val\n";
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}
