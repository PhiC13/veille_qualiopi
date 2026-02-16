<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Scripts internes -->
<script src="/assets/js/feeds-local.js"></script>
<script src="/assets/js/feeds-external.js"></script>
<script src="/assets/js/activity-chart.js"></script>
<script src="/assets/js/main.js"></script>

<!-- Pipeline + mini-log -->
<script>
fetch("/xml/last_update.json")
  .then(r => r.json())
  .then(data => {
    const date = new Date(data.last_update);
    const formatted = date.toLocaleString("fr-FR");

    const badge = document.getElementById("pipeline-status");
    badge.textContent = "🟢 Mise à jour : " + formatted;
    badge.classList.add("pipeline-ok");

    document.getElementById("pipeline-status-box").innerHTML =
      `<div class="pipeline-ok-box">
         🟢 Mise à jour OK<br>
         Dernière mise à jour : <strong>${formatted}</strong>
       </div>`;
  })
  .catch(() => {
    const badge = document.getElementById("pipeline-status");
    badge.textContent = "🔴 Echec de la mise à jour";
    badge.classList.add("pipeline-ko");

    document.getElementById("pipeline-status-box").innerHTML =
      `<div class="pipeline-ko-box">
         🔴 Echec de la mise à jour<br>
         Impossible de lire last_update.json
       </div>`;
  });

fetch("/xml/update_history.log")
  .then(r => r.text())
  .then(text => {
    const lines = text.trim().split("\n").slice(-3).reverse();
    document.getElementById("update-history").innerHTML =
      "<ul>" + lines.map(l => `<li>${l}</li>`).join("") + "</ul>";
  })
  .catch(() => {
    document.getElementById("update-history").innerHTML =
      "<em>Historique indisponible</em>";
  });
</script>
<?php if (($active ?? "") === "sources"): ?>
<script src="/assets/js/admin-core.js"></script>
<script src="/assets/js/admin-ui.js"></script>
<?php endif; ?>
