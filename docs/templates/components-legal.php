<?php

function component_sidebar_legal() {
    ?>
    <aside class="sidebar">

        <div class="sidebar-block">
            <h3>📘 Sommaire</h3>
            <ul id="legal-toc" class="toc-list"></ul>
        </div>

        <div class="sidebar-block">
            <h3>⏱️ Mise à jour</h3>
            <p id="legal-last-update">Chargement…</p>
        </div>

        <div class="sidebar-block">
            <h3>⚙️ Administration</h3>
            <ul class="toc-list">
                <li><a href="/legal/admin-legal.html">📝 Gérer les articles</a></li>
                <li><a href="/legal/data/legal.json" target="_blank">🧾 JSON source</a></li>
            </ul>
        </div>

    </aside>
    <?php
}

