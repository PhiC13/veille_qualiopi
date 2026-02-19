<?php
$title = "Logs du pipeline et des sources";
$active = "logs";
$show_sidebar = false;

$extra_css = [
    "/docs/assets/css/admin.css"
];

ob_start();
include __DIR__ . "/admin-logs-content.php";
$content = ob_get_clean();

include __DIR__ . "/../templates/base.php";
