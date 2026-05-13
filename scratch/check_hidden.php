<?php
$pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=tarrillo', 'postgres', 'elmasbravo');
$stmt = $pdo->query('SELECT id, tipo, cantidad_unitaria, cantidad_total, tipo_comprobante, ubicacion_id FROM kardexes WHERE producto_id = 6 ORDER BY id ASC');
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->query('SELECT id FROM tipo_comprobantes');
$comps = $stmt2->fetchAll(PDO::FETCH_COLUMN);

foreach ($res as &$r) {
    $r['comprobante_exists'] = in_array($r['tipo_comprobante'], $comps);
}

echo json_encode($res);
