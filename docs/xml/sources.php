<?php
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=veille_local;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sections = $pdo->query("SELECT id, code FROM sections ORDER BY ordre ASC")->fetchAll(PDO::FETCH_ASSOC);

$result = [];

foreach ($sections as $section) {

    $stmt = $pdo->prepare("
        SELECT label
        FROM sources
        WHERE section_id = ?
        ORDER BY ordre ASC
    ");
    $stmt->execute([$section['id']]);
    $labels = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $paths = [];

    foreach ($labels as $label) {

        // Si le label commence déjà par "rss_", on ne le double pas
        if (str_starts_with($label, "rss_")) {
            $filename = $label . ".xml";
        } else {
            $filename = "rss_" . $label . ".xml";
        }

        // On génère le chemin attendu par feeds.js
        $paths[] = "xml/" . $filename;
    }

    $result[$section['code']] = $paths;
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
