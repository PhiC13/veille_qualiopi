<?php

// Charger la config
$config = require __DIR__ . '/../../config.php';

// Créer la connexion PDO
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

header('Content-Type: text/plain; charset=utf-8');

// On récupère les 20 dernières activités, mais en ordre ASC pour que le JS prenne les 3 dernières
$sql = "SELECT timestamp, count FROM activity ORDER BY timestamp ASC LIMIT 20";
$stmt = $pdo->query($sql);

$lines = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // Formatage de la date
    $date = date('d/m/Y', strtotime($row['timestamp']));
    $time = date('H:i:s', strtotime($row['timestamp']));
    $count = (int)$row['count'];

    // Format final demandé
    $lines[] = "$date - $time -> OK ($count articles)";
}

// Sortie texte brute
echo implode("\n", $lines);
