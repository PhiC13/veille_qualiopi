<?php
$title = "Historique du pipeline";
$active = "pipeline";
$show_sidebar = false;

$extra_css = [
    "/docs/assets/css/admin.css",
    "/docs/assets/css/admin-pipeline.css",
];


ob_start();
include __DIR__ . "/admin-pipeline-content.php";
$content = ob_get_clean();

include __DIR__ . "/../templates/base.php";
