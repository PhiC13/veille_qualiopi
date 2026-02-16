<?php

/* =========================================================
   HEADER
========================================================= */
function component_header() {
    ?>
    <header>
        <img src="/img/Logo_LBJ-13A.png" alt="Logo Le Bateau Jaune" class="logo">
        <div class="title-block">
            <h1>Veille réglementaire, pédagogique et métiers</h1>
            <p>Agrégateur</p>
        </div>
    </header>
    <?php
}

/* =========================================================
   NAVIGATION
========================================================= */
function component_nav($active = "") {
    ?>
    <nav class="top-menu">
        <div class="nav-left">
            <a href="/pages/index.php">Accueil</a>
            <a href="/pages/legal.php">Références légales</a>
        </div>

        <div class="nav-center">
            <input id="searchInput" type="text" placeholder="Rechercher…">
        </div>

        <div class="nav-right">

            <?php if ($active !== "legal") component_pipeline_badge(); ?>

            <!-- Icône paramètres à l’extrême droite -->
            <a href="/pages/admin-<?= $active ?>.php" 
               class="settings-link" 
               title="Administration de cette page">
                ⚙️
            </a>

        </div>
    </nav>
    <?php
}


/* =========================================================
   BADGE PIPELINE — VERSION IDENTIQUE À L’ORIGINAL
========================================================= */
function component_pipeline_badge() {

    // Lecture du fichier JSON last_update.json
    $jsonPath = __DIR__ . "/../xml/last_update.json";
    $lastUpdate = "Chargement…";

    if (file_exists($jsonPath)) {
        $data = json_decode(file_get_contents($jsonPath), true);
        if (!empty($data["last_update"])) {
            $date = new DateTime($data["last_update"]);
            $lastUpdate = "Mise à jour : " . $date->format("d/m/Y H:i:s");
        }
    }

    // ⚠️ IMPORTANT : on NE CHANGE PAS la structure interne
    ?>
    <a href="/xml/update_history.log" target="_blank" class="pipeline-link">
        <span id="pipeline-status" class="pipeline-badge">
            ⟳ <?= $lastUpdate ?>
        </span>
    </a>
    <?php
}



/* =========================================================
   SIDEBAR
========================================================= */
function component_sidebar() {
    ?>
    <aside class="sidebar">

        <!-- Bloc 1 : Activité récente -->
        <div class="sidebar-block">
            <h3><span class="icon">📊</span> Activité récente</h3>
            <div class="activity-controls">
                <label>Affichage :</label>
                <select id="activityMode">
                    <option value="day" selected>Jour</option>
                    <option value="week">Semaine</option>
                    <option value="month">Mois</option>
                </select>

                <label>Période :</label>
                <select id="activityRange">
                    <option value="7">7 jours</option>
                    <option value="30" selected>30 jours</option>
                    <option value="90">90 jours</option>
                    <option value="all">Tout</option>
                </select>
            </div>
            <canvas id="activityChart" width="300" height="200"></canvas>
        </div>

        <!-- Bloc 2 : Mises à jour -->
        <div class="sidebar-block">
            <h3><span class="icon">⏱️</span> Mises à jour</h3>
            <div id="pipeline-status-box" class="update-box"></div>
            <div id="update-history" class="update-history"></div>

            <a href="/xml/update_history.log" target="_blank" class="history-link">Voir le log complet</a>
            <a href="/dashboard.php" target="_blank" class="history-link">Dashboard COSMOS</a>
            <a href="/xml/logs.jsonl" target="_blank" class="history-link">Logs</a>
        </div>

        <!-- Bloc 3 : Derniers articles -->
        <div class="sidebar-block">
            <h3><span class="icon">📰</span> Derniers articles</h3>
            <div id="articles-global"></div>
        </div>

    </aside>
    <?php
}

/* =========================================================
   FOOTER
========================================================= */
function component_footer() {

    // Date de dernière modification du fichier courant
    $timestamp = filemtime($_SERVER['SCRIPT_FILENAME']);

    // Format anglais → ex: "February 2026"
    $monthYear = date("F Y", $timestamp);

    // Traduction des mois
    $mois = [
        "January" => "Janvier",
        "February" => "Février",
        "March" => "Mars",
        "April" => "Avril",
        "May" => "Mai",
        "June" => "Juin",
        "July" => "Juillet",
        "August" => "Août",
        "September" => "Septembre",
        "October" => "Octobre",
        "November" => "Novembre",
        "December" => "Décembre"
    ];

    foreach ($mois as $en => $fr) {
        $monthYear = str_replace($en, $fr, $monthYear);
    }
    ?>

    <footer>
        ©︎ <?= $monthYear ?> – Agrégateur RSS – Philippe (PhiC13) – Le Bateau Jaune
    </footer>

    <?php
}
