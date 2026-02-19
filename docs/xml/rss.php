<?php
header('Content-Type: application/xml; charset=utf-8');

$label = $_GET['label'] ?? null;
if (!$label) {
    echo "<!-- Aucun label fourni -->";
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=veille_local;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Normalisation du label : éviter rss_rss_xxx
$cleanLabel = $label;

// Si le label commence déjà par "rss_", on ne le double pas
if (str_starts_with($cleanLabel, "rss_")) {
    $cleanLabel = substr($cleanLabel, 4); // retire "rss_"
}

// On recherche la source dans la base
$stmt = $pdo->prepare("
    SELECT id, label
    FROM sources
    WHERE label = ? OR label = ?
    LIMIT 1
");
$stmt->execute([
    $cleanLabel,
    "rss_" . $cleanLabel
]);

$source = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$source) {
    echo "<!-- Source introuvable : $label -->";
    exit;
}

$source_id = $source['id'];

// Récupération des articles
$stmt = $pdo->prepare("
    SELECT title, url, published_at
    FROM articles
    WHERE source_id = ?
    ORDER BY published_at DESC
    LIMIT 50
");
$stmt->execute([$source_id]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Génération du flux XML
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss version="2.0">
<channel>
    <title><?= htmlspecialchars($source['label']) ?></title>
    <link></link>
    <description>Flux dynamique généré depuis la base SQL</description>

<?php foreach ($articles as $a): ?>
    <item>
        <title><?= htmlspecialchars($a['title']) ?></title>
        <link><?= htmlspecialchars($a['url']) ?></link>
        <pubDate><?= date(DATE_RSS, strtotime($a['published_at'])) ?></pubDate>
    </item>
<?php endforeach; ?>

</channel>
</rss>
