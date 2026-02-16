<?php
$title = "Veille Le Bateau Jaune – Philippe";
$active = "home"; // pour surligner Accueil dans le menu

// Pas de CSS spécifique pour index
$extra_css = [];

ob_start();
include __DIR__ . "/index-content.php";
$content = ob_get_clean();

include __DIR__ . "/../templates/base.php";
