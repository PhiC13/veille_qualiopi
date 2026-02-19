<?php

// Charger la config
$config = require __DIR__ . '/../../config.php';

// Créer la connexion PDO
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

header('Content-Type: application/jsonl; charset=utf-8');

// Récupérer l'activité
$sql = "SELECT timestamp, count FROM activity ORDER BY timestamp ASC";
$stmt = $pdo->query($sql);

// Sortie JSONL (une ligne JSON par ligne)
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode([
        "timestamp" => date('c', strtotime($row['timestamp'])),
        "count" => (int)$row['count']
    ], JSON_UNESCAPED_SLASHES) . "\n";
}
