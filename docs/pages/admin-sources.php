<?php
$title = "Administration des sources";
$active = "sources";
$show_sidebar = false;

$extra_css = [
    "/docs/assets/css/admin.css"
];

ob_start();
include __DIR__ . "/admin-sources-content.php";
$content = ob_get_clean();

include __DIR__ . "/../templates/base.php";
