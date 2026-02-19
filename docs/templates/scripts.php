<!-- scripts.php -->

<!-- Chart.js (si tu en as encore besoin) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Scripts internes -->
<script src="/docs/assets/js/feeds.js?v=<?= time() ?>" defer></script>
<script src="/docs/assets/js/activity-chart.js?v=3" defer></script>
<script src="/docs/assets/js/main.js?v=<?= time() ?>" defer></script>

<!-- Pipeline + mini-log -->
<script>
// ------------------------------------------------------------
// Bloc 1 : pipeline-status
// ------------------------------------------------------------
const badge = document.getElementById("pipeline-status");
if (badge) {
    fetch("/docs/xml/last_update.php")
      .then(r => r.json())
      .then(data => {
        const date = new Date(data.last_update);
        const formatted = date.toLocaleString("fr-FR", { timeZone: "UTC" });

        badge.textContent = "🟢 Mise à jour : " + formatted;
        badge.classList.add("pipeline-ok");

        const box = document.getElementById("pipeline-status-box");
        if (box) {
            box.innerHTML =
              `<div class="pipeline-ok-box">
                 🟢 Mise à jour OK<br>
                 Dernière mise à jour : <strong>${formatted}</strong>
               </div>`;
        }
      })
      .catch(() => {
        badge.textContent = "🔴 Echec de la mise à jour";
        badge.classList.add("pipeline-ko");

        const box = document.getElementById("pipeline-status-box");
        if (box) {
            box.innerHTML =
              `<div class="pipeline-ko-box">
                 🔴 Echec de la mise à jour<br>
                 Impossible de lire last_update.php
               </div>`;
        }
      });
}
</script>

<script>
// ------------------------------------------------------------
// Bloc 2 : update-history
// ------------------------------------------------------------
const historyBox = document.getElementById("update-history");
if (historyBox) {
    fetch("/docs/xml/update_history.php")
      .then(r => r.text())
      .then(text => {
        const lines = text.trim().split("\n");
        const previous = lines.slice(0, -1);
        const lastThreeBefore = previous.slice(-3).reverse();

        historyBox.innerHTML =
          "<ul>" + lastThreeBefore.map(l => `<li>${l}</li>`).join("") + "</ul>";
      })
      .catch(() => {
        historyBox.innerHTML = "<em>Historique indisponible</em>";
      });
}
</script>

<script>
// ------------------------------------------------------------
// Bloc 3 : articles-global
// ------------------------------------------------------------
const container = document.getElementById("articles-global");
if (container) {
    fetch("/docs/xml/latest_articles.php")
      .then(r => r.json())
      .then(articles => {
        let html = "";
        articles.forEach(a => {
          const date = new Date(a.published_at);
          const formatted = date.toLocaleDateString("fr-FR");

          html += `
            <div class="article-mini">
              <a href="${a.url}" target="_blank">${a.title}</a>
              <small>${formatted} – ${a.source}</small>
            </div>
          `;
        });
        container.innerHTML = html;
      })
      .catch(() => {
        container.innerHTML = "<em>Impossible de charger les articles</em>";
      });
}
</script>
