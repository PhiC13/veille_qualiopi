async function loadExternalRSS(url, containerId, label) {
    const container = document.getElementById(containerId);
    if (!container) return;

    try {
        const api = "https://api.rss2json.com/v1/api.json?rss_url=" + encodeURIComponent(url);
        const response = await fetch(api);
        const data = await response.json();

        const items = data.items.slice(0, 10);

        const details = document.createElement("details");
        details.className = "article-group";

        const summary = document.createElement("summary");
        summary.textContent = `${label} (${items.length})`;
        details.appendChild(summary);

        if (items.length === 0) {
            details.classList.add("empty-source");
            details.addEventListener("click", e => {
                e.preventDefault();
                e.stopPropagation();
            });
        }

        container.appendChild(details);

        items.forEach(item => {
            const div = document.createElement("div");
            div.className = "article-mini";
            div.innerHTML = `
                <a href="${item.link}" target="_blank">${item.title}</a>
                <small>${item.pubDate}</small>
            `;
            details.appendChild(div);
        });

    } catch (err) {
        console.error("Erreur flux externe :", url, err);
    }
}

function loadExternalFeeds() {
    loadExternalRSS("https://www.service-public.fr/rss/actualites.rss", "articles-legal", "Service-public.fr");
    loadExternalRSS("https://travail-emploi.gouv.fr/actualites.rss", "articles-legal", "Travail-emploi.gouv.fr");
    loadExternalRSS("https://www.economie.gouv.fr/rss/actualites", "articles-legal", "Economie.gouv.fr");

    loadExternalRSS("https://www.digiforma.com/feed/", "articles-pedago", "Digiforma");
}

document.addEventListener("DOMContentLoaded", loadExternalFeeds);
