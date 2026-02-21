<?php
$config = require __DIR__ . '/../../config.php';
$BASE = $config['base_url'];
?>

<?php

/* =========================================================
   HEADER
========================================================= */
function component_header() {
    global $BASE;
    ?>
    <header>
        <img src="<?= $BASE ?>/docs/img/Logo_LBJ-13A.png" alt="Logo Le Bateau Jaune" class="logo">
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
    global $BASE;
    ?>
    <nav class="top-menu">
        <div class="nav-left">
            <a href="<?= $BASE ?>/docs/pages/index.php" class="<?= $active === 'index' ? 'active' : '' ?>">Accueil</a>
            <a href="<?= $BASE ?>/docs/pages/legal.php" class="<?= $active === 'legal' ? 'active' : '' ?>">Références légales</a>
        </div>

        <div class="nav-center">
            <input id="searchInput" type="text" placeholder="Rechercher…">
        </div>

        <div class="nav-right">
            <?php if ($active !== "legal") component_pipeline_badge(); ?>

            <div class="admin-menu">
                <button class="settings-link" title="Administration">⚙️</button>

                <div class="admin-dropdown">
                    <div class="admin-title">Administration</div>

                    <a href="<?= $BASE ?>/docs/pages/admin-sources.php">Sources</a>
                    <a href="<?= $BASE ?>/docs/pages/admin-sections.php">Sections</a>
                    <a href="<?= $BASE ?>/docs/pages/admin-pipeline.php">Logs Pipeline</a>
                    <a href="<?= $BASE ?>/docs/pages/admin-logs.php">Logs</a>
                </div>
            </div>
        </div>
    </nav>
    <?php
}


/* =========================================================
   BADGE PIPELINE
========================================================= */
function component_pipeline_badge() {
    global $BASE;

    $lastUpdate = "Chargement…";
    ?>
    <a href="<?= $BASE ?>/docs/xml/update_history.php" target="_blank" class="pipeline-link">
        <span id="pipeline-status" class="pipeline-badge">⟳ <?= $lastUpdate ?></span>
    </a>
    <?php
}


/* =========================================================
   SIDEBAR
========================================================= */
function component_sidebar() {
    global $BASE;
    ?>
    <aside class="sidebar">

        <!-- Bloc 1 : Activité récente -->
        <div class="sidebar-block">
            <h3><span class="icon">📊</span> Activité récente</h3>

            <div class="activity-controls">
                <label for="activityMode">Mode :</label>
                <select id="activityMode">
                    <option value="day">Jour</option>
                    <option value="week">Semaine</option>
                    <option value="month">Mois</option>
                </select>

                <label for="activityRange">Période :</label>
                <select id="activityRange">
                    <option value="7">7 jours</option>
                    <option value="30">30 jours</option>
                    <option value="90">90 jours</option>
                    <option value="all">Tout</option>
                </select>
            </div>

            <canvas id="activityChart" width="400" height="250"></canvas>
        </div>

        <!-- Bloc 2 : Mises à jour -->
        <div class="sidebar-block">
            <h3><span class="icon">⏱️</span> Mises à jour</h3>
            <div id="pipeline-status-box" class="update-box"></div>
            <div id="update-history" class="update-history"></div>

            <a href="<?= $BASE ?>/docs/xml/update_history.php" target="_blank" class="history-link">Voir le log complet</a>
            <a href="/dashboard.php" target="_blank" class="history-link">Dashboard COSMOS</a>
            <a href="<?= $BASE ?>/docs/xml/logs.jsonl" target="_blank" class="history-link">Logs</a>
        </div>

        <!-- Bloc 3 : Derniers articles -->
        <div class="sidebar-block">
            <h3><span class="icon">📰</span> Derniers articles</h3>
            <div id="articles-global" class="articles-global"></div>
        </div>

    </aside>
    <?php
}


/* =========================================================
   FOOTER
========================================================= */
function component_footer() {
    $timestamp = filemtime($_SERVER['SCRIPT_FILENAME']);
    $monthYear = date("F Y", $timestamp);

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
