<?php
require_once __DIR__ . '/../../db_conn.php';

$pdo = get_pdo(); // ← indispensable

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
