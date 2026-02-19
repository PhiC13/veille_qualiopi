<?php
header('Content-Type: application/xml; charset=utf-8');

$sectionCode = $_GET['section'] ?? null;
if (!$sectionCode) {
    echo "<!-- Section manquante -->";
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=veille_local;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier que la section existe
$stmt = $pdo->prepare("
    SELECT id, label
    FROM sections
    WHERE code = ?
    LIMIT 1
");
$stmt->execute([$sectionCode]);
$section = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$section) {
    echo "<!-- Section inconnue : $sectionCode -->";
    exit;
}

$section_id = $section['id'];

// Récupérer tous les articles de la section
$stmt = $pdo->prepare("
    SELECT a.title, a.url, a.published_at, s.label AS source_label
    FROM articles a
    JOIN sources s ON a.source_id = s.id
    WHERE s.section_id = ?
    ORDER BY a.published_at DESC
    LIMIT 200
");
$stmt->execute([$section_id]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Génération du flux XML
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss version="2.0">
<channel>
    <title>Flux consolidé – <?= htmlspecialchars($section['label']) ?></title>
    <link></link>
    <description>Tous les articles de la section <?= htmlspecialchars($section['label']) ?></description>

<?php foreach ($articles as $a): ?>
    <item>
        <title><?= htmlspecialchars($a['title']) ?></title>
        <link><?= htmlspecialchars($a['url']) ?></link>
        <pubDate><?= date(DATE_RSS, strtotime($a['published_at'])) ?></pubDate>
        <source><?= htmlspecialchars($a['source_label']) ?></source>
    </item>
<?php endforeach; ?>

</channel>
</rss>
