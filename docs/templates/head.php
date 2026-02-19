<!-- head.php -->
 <meta charset="UTF-8">
<title><?= $title ?? "Le Bateau Jaune" ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSS global -->
<link rel="stylesheet" href="<?= $BASE_URL ?>/docs/assets/css/theme.css">
<link rel="stylesheet" href="<?= $BASE_URL ?>/docs/assets/css/layout.css">
<link rel="stylesheet" href="<?= $BASE_URL ?>/docs/assets/css/components.css">


<!-- CSS spécifiques -->
<?php if (!empty($extra_css)): ?>
    <?php foreach ($extra_css as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
<?php endif; ?>
