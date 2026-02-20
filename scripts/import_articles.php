<?php

require_once __DIR__ . '/../../db_conn.php';

$pdo = get_pdo(); // ← indispensable

// Récupérer toutes les sources externes
$sources = $pdo->query("SELECT id, url FROM sources WHERE type = 'external'")->fetchAll(PDO::FETCH_ASSOC);

foreach ($sources as $src) {

    // On récupère juste le nom du fichier XML
    $filename = basename($src['url']);

    // Le fichier XML est dans le même dossier que ce script
    $xmlFile = __DIR__ . "/" . $filename;

    if (!file_exists($xmlFile)) {
        echo "Fichier XML introuvable : {$xmlFile}\n";
        continue;
    }

    $xml = simplexml_load_file($xmlFile);

    if (!$xml || !isset($xml->channel->item)) {
        echo "XML invalide ou vide : {$xmlFile}\n";
        continue;
    }

    foreach ($xml->channel->item as $item) {

        $title = (string)$item->title;
        $url = (string)$item->link;

        $pubDate = isset($item->pubDate)
            ? date('Y-m-d H:i:s', strtotime((string)$item->pubDate))
            : date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("
            INSERT IGNORE INTO articles (source_id, title, url, published_at, scraped_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $src['id'],
            $title,
            $url,
            $pubDate
        ]);
    }

    echo "Articles importés pour la source {$src['id']} ({$filename})\n";
}

echo "Import XML terminé.\n";
