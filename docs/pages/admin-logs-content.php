<main class="admin-container">

    <section>
		<h2>
			Logs du pipeline & des sources
			<span id="logs-total" class="pipe-total"></span>
		</h2>


        <label for="log-type">Type de logs :</label>
        <select id="log-type">
            <option value="pipeline">Pipeline</option>
            <option value="sources">Sources</option>
        </select>
    </section>

    <section id="logs-list" class="sources-cards"></section>

    <div class="form-actions" style="margin-top:20px;">
        <button id="log-prev" class="btn-secondary">← Précédent</button>
        <button id="log-next" class="btn-primary">Suivant →</button>
    </div>

    <!-- PAGINATION NUMÉROTÉE -->
    <div id="logs-pagination" class="pagination"></div>

</main>

<script src="/docs/assets/js/admin/logs.js"></script>
