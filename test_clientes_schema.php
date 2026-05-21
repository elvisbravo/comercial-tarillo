<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->query("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'clientes'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['column_name']} ({$row['data_type']}) - Nullable: {$row['is_nullable']}\n";
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
