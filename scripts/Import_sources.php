<?php

require_once __DIR__ . '/../../db_conn.php';

$pdo = get_pdo(); // ← indispensable

// Charger le JSON
$file = __DIR__ . "/sources.json";
$json = file_get_contents($file);
$data = json_decode($json, true);

foreach ($data as $sectionCode => $files) {

    // Trouver l'ID de la section
    $stmt = $pdo->prepare("SELECT id FROM sections WHERE code = ?");
    $stmt->execute([$sectionCode]);
    $section_id = $stmt->fetchColumn();

    if (!$section_id) {
        echo "Section inconnue : $sectionCode\n";
        continue;
    }

    // Parcourir les fichiers XML de la section
    foreach ($files as $index => $xmlPath) {

        // Déduire un label simple depuis le nom du fichier
        $label = basename($xmlPath, ".xml");

        // Insérer la source
        $stmt = $pdo->prepare("
            INSERT INTO sources (section_id, label, url, type, actif, ordre)
            VALUES (?, ?, ?, 'external', 1, ?)
        ");

        $stmt->execute([
            $section_id,
            $label,
            $xmlPath,
            $index
        ]);

        echo "Importé : [$sectionCode] $label\n";
    }
}

echo "Import terminé.\n";
