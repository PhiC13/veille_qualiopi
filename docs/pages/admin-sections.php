<?php
$title = "Administration des sections";
$active = "sections";
$show_sidebar = false;

$extra_css = [
    "/docs/assets/css/admin.css"
];

ob_start();
include __DIR__ . "/admin-sections-content.php";
$content = ob_get_clean();

include __DIR__ . "/../templates/base.php";
