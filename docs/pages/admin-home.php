<?php
$title = "Administration des sources RSS";
$active = "sources";
$show_sidebar = false;   // Désactive la colonne de droite

$extra_css = [
    "/assets/css/admin.css"
];

ob_start();
include __DIR__ . "/admin-sources-content.php";
$content = ob_get_clean();

include __DIR__ . "/../templates/base.php";
