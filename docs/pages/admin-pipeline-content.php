<main class="admin-container">

    <section>
		<h2>
			Historique du pipeline
			<span id="pipeline-total" class="pipe-total"></span>
		</h2>

    </section>

    <!-- LISTE DES RUNS -->
	  <section id="pipeline-list" class="admin-list"></section>


    <div class="form-actions" style="margin-top:20px;">
        <button id="pipe-prev" class="btn-secondary">← Précédent</button>
        <button id="pipe-next" class="btn-primary">Suivant →</button>
    </div>

	<!-- PAGINATION NUMÉROTÉE -->
	<div id="pipeline-pagination" class="pagination"></div>

    <hr>

    <!-- DÉTAIL D’UN RUN -->
	<section id="pipeline-details" class="admin-list"></section>


</main>

<script src="/docs/assets/js/admin/pipeline.js"></script>
