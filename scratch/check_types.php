<?php
$pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=tarrillo', 'postgres', 'elmasbravo');
$stmt = $pdo->query('SELECT DISTINCT tipo FROM kardexes');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
