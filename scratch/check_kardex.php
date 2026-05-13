<?php
$pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=tarrillo', 'postgres', 'elmasbravo');
$stmt = $pdo->query('SELECT id, tipo, cantidad_unitaria, cantidad_total, fecha FROM kardexes WHERE producto_id = 6 ORDER BY fecha ASC, id ASC');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
