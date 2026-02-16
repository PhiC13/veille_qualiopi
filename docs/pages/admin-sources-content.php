<main class="admin-container">

    <!-- TOKEN -->
    <section class="token-box" aria-labelledby="token-title">
      <h2 id="token-title" class="visually-hidden">Token GitHub</h2>

      <label for="token">Token GitHub (fine-grained)</label>
      <input type="password" id="token" placeholder="ghp_xxxxx" autocomplete="off">
      <button id="save-token" class="btn-primary">Enregistrer le token</button>
      <br> 
      <small class="token-help">
        Le token est stocké uniquement dans ce navigateur (localStorage).
      </small>
    </section>

    <!-- CARTES DES CATÉGORIES -->
    <section id="sources-list" class="sources-cards" aria-labelledby="sources-title">
      <h2 id="sources-title" class="visually-hidden">Sources par catégorie</h2>
    </section>

    <hr>

    <!-- FORMULAIRE EXTERNAL_* -->
    <section aria-labelledby="form-title">
      <h2 id="form-title">Ajouter un flux distant</h2>

      <form id="source-form" class="source-form" novalidate>

        <div class="form-row">

          <div class="form-group">
            <label for="src-cat">Catégorie</label><br>
            <select id="src-cat">
              <option value="">— Choisir une catégorie —</option>
              <option value="legal">Légal</option>
              <option value="pedago">Pédagogie</option>
              <option value="metiers">Métiers</option>
            </select>
          </div>

          <div class="form-group" style="flex:1;">
            <label for="src-url">URL du flux RSS</label><br>
            <input type="url" id="src-url" required aria-required="true" placeholder="https://…">
          </div>

        </div>

        <input type="hidden" id="src-index">

        <div id="validation-status" role="status" aria-live="polite"></div>

        <div class="form-actions">
          <button type="submit" class="btn-primary">Ajouter</button>
          <button type="button" id="cancel-edit" class="btn-secondary" style="display:none;">Annuler</button>
        </div>

      </form>
    </section>

    <!-- EXPORT SOURCES.PY -->
    <section class="export-box" aria-labelledby="export-title">
      <h2 id="export-title">Sauvegarde / Export</h2>
      <p>
        Cette interface modifie uniquement les listes <code>EXTERNAL_*</code> dans 
        <code>docs/sources.py</code>. Les listes <code>LOCAL_*</code> sont affichées en lecture seule.
      </p>
      <button id="export-sources" class="btn-save">Sauvegarder les sources</button>
    </section>

</main>
