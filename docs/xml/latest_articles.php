<?php

// Charger la config
$config = require __DIR__ . '/../../config.php';

// Créer la connexion PDO
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

header('Content-Type: application/json; charset=utf-8');

// Récupérer les 10 derniers articles
$sql = "
SELECT a.title, a.url, a.published_at,
       s.label AS source,
       sec.label AS section
FROM articles a
JOIN sources s ON a.source_id = s.id
JOIN sections sec ON s.section_id = sec.id
ORDER BY a.published_at DESC
LIMIT 10
";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatage ISO 8601
foreach ($data as &$row) {
    $row['published_at'] = date('c', strtotime($row['published_at']));
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
