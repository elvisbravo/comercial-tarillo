<?php
$host = '127.0.0.1';
$db   = 'tarrillo';
$user = 'postgres';
$pass = 'grupoes2026';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->query("SELECT id, fecha, hora, serie, correlativo, motivo, cliente_id, user_id FROM traslados WHERE serie = 'CAR'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        var_dump($row);
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
