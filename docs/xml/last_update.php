<?php

// Charger la config
$config = require __DIR__ . '/../../config.php';

// Créer la connexion PDO
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

header('Content-Type: application/json; charset=utf-8');

// Récupérer la dernière activité
$sql = "SELECT timestamp FROM activity ORDER BY timestamp DESC LIMIT 1";
$stmt = $pdo->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Sortie JSON identique à last_update.json
echo json_encode([
    "last_update" => $row ? date('c', strtotime($row['timestamp'])) : null
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
