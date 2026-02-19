<main class="admin-container">

    <!-- TITRE -->
    <section aria-labelledby="sections-title">
        <h2 id="sections-title" class="visually-hidden">Administration des sections</h2>
    </section>

    <!-- LISTE DES SECTIONS (rendue par JS) -->
    <section id="sections-list" class="admin-list"></section>

    <hr>

    <!-- FORMULAIRE AJOUT / EDITION -->
    <section aria-labelledby="form-title">
        <h2 id="form-title">Ajouter une section</h2>

        <form id="section-form" class="admin-form" novalidate>

            <div class="form-row">

                <!-- CODE -->
                <div class="form-group">
                    <label for="sec-code">Code</label><br>
                    <input type="text" id="sec-code" required placeholder="legal / pedago / metiers…">
                </div>

                <!-- LABEL -->
                <div class="form-group">
                    <label for="sec-label">Nom de la section</label><br>
                    <input type="text" id="sec-label" required placeholder="Volet légal…">
                </div>

            </div>

            <div class="form-row">

                <!-- ORDRE -->
                <div class="form-group">
                    <label for="sec-ordre">Ordre</label><br>
                    <input type="number" id="sec-ordre" value="0" min="0" style="width:80px;">
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer</button>
                <button type="button" id="cancel-edit" class="btn-secondary" style="display:none;">Annuler</button>
            </div>

        </form>
    </section>

</main>

<script src="/docs/assets/js/admin/sections.js"></script>
