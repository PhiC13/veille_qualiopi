<?php
$pdo = new PDO('mysql:host=localhost;dbname=veille_local;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Charger les sections
$sections = $pdo->query("
    SELECT id, code, label
    FROM sections
    ORDER BY ordre ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($sections as $section): ?>
<section class="root-section" data-section="<?= htmlspecialchars($section['code']) ?>">
    <h2><?= htmlspecialchars($section['label']) ?></h2>
</section>
<?php endforeach; ?>
