<main class="admin-container">

    <!-- TITRE -->
    <section aria-labelledby="sources-title">
        <h2 id="sources-title" class="visually-hidden">Administration des sources</h2>
    </section>

    <!-- LISTE DES SOURCES (rendue par JS) -->
    <section id="sources-list" class="admin-list"></section>

    <hr>

    <!-- FORMULAIRE AJOUT / EDITION -->
    <section aria-labelledby="form-title">
        <h2 id="form-title">Ajouter une source</h2>

        <form id="source-form" class="admin-form" novalidate>

            <div class="form-row">

                <!-- LABEL -->
                <div class="form-group">
                    <label for="src-label">Nom de la source</label>
                    <input type="text" id="src-label" required placeholder="Nom lisible…">
                </div>

                <!-- URL -->
                <div class="form-group">
                    <label for="src-url">URL</label>
                    <input type="url" id="src-url" required placeholder="https://…">
                </div>

            </div>

            <div class="form-row">

                <!-- SECTION -->
                <div class="form-group">
                    <label for="src-section">Section</label>
                    <select id="src-section" required></select>
                </div>

                <!-- ORDRE -->
                <div class="form-group">
                    <label for="src-ordre">Ordre</label>
                    <input type="number" id="src-ordre" value="0" min="0" style="width:80px;">
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer</button>
                <button type="button" id="cancel-edit" class="btn-secondary" style="display:none;">Annuler</button>
            </div>

        </form>
    </section>

</main>

<!-- JS ADMIN SOURCES -->
<script src="/docs/assets/js/admin/sources.js"></script>
