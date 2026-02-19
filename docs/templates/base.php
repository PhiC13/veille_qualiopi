<?php
$config = require __DIR__ . '/../../config.php';
$BASE_URL = $config['base_url'];
?>
<?php
require_once __DIR__ . "/components.php";

// Charger les composants spécifiques si la page en a besoin
if (($active ?? "") === "legal") {
    require_once __DIR__ . "/components-legal.php";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . "/head.php"; ?>
</head>

<body>

    <?php component_header(); ?>
    <?php component_nav($active ?? ""); ?>

    <div class="layout">
        <div class="main-column">
            <?= $content ?>
        </div>

        <?php
        // ---------------------------------------------
        // Sidebar optionnelle (admin = pas de sidebar)
        // ---------------------------------------------
        $show_sidebar = $show_sidebar ?? true;

        if ($show_sidebar) {
            if (($active ?? "") === "legal") {
                component_sidebar_legal();
            } else {
                component_sidebar();
            }
        }
        ?>
    </div>

    <?php component_footer(); ?>
    <?php include __DIR__ . "/scripts.php"; ?>

</body>
</html>
