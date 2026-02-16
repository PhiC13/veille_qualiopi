<?php
$title = "Références légales – Le Bateau Jaune";
$active = "legal";

$extra_css = [
    "/assets/css/pages/legal.css"
];

ob_start();
include __DIR__ . "/legal-content.php";
$content = ob_get_clean();

include __DIR__ . "/../templates/base.php";
