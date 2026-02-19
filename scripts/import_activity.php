<?php

// Charger la config
$config = require __DIR__ . '/../config.php';

// Créer la connexion PDO
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Fichier JSONL d'origine
$filepath = __DIR__ . '/../docs/xml/activity_history.jsonl';

if (!file_exists($filepath)) {
    die("Fichier introuvable : $filepath\n");
}

$pdo->exec("TRUNCATE TABLE activity");

$handle = fopen($filepath, "r");
if (!$handle) {
    die("Impossible d'ouvrir le fichier.\n");
}

$insert = $pdo->prepare("
    INSERT INTO activity (timestamp, count)
    VALUES (:timestamp, :count)
");

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    if ($line === "") continue;

    $json = json_decode($line, true);
    if (!$json) {
        echo "Ligne ignorée (JSON invalide) : $line\n";
        continue;
    }

    // Conversion ISO8601 → datetime SQL
    $ts = date('Y-m-d H:i:s', strtotime($json['timestamp']));

    $insert->execute([
        ':timestamp' => $ts,
        ':count' => $json['count']
    ]);
}

fclose($handle);

echo "Import terminé avec succès.\n";
