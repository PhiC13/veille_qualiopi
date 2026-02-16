// ======================================================================
//  Formatage FR
// ======================================================================
function formatDateFR(rawDate) {
    if (!rawDate) return "";
    return new Date(rawDate).toLocaleString("fr-FR", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
}

// ======================================================================
//  Lecture d'un flux RSS local
// ======================================================================
async function loadLocalRSS(url, containerId, label) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Création du bloc source AVANT le fetch
    const details = document.createElement("details");
    details.className = "article-group";

    const summary = document.createElement("summary");
    summary.textContent = `${label} (0)`;
    details.appendChild(summary);

    container.appendChild(details); // <-- AJOUT IMMÉDIAT

    try {
        const response = await fetch(url);
        const text = await response.text();

        const parser = new DOMParser();
        const xml = parser.parseFromString(text, "text/xml");

        let items = Array.from(xml.querySelectorAll("item")).map(item => {
            const title = item.querySelector("title")?.textContent ?? "Sans titre";
            const link = item.querySelector("link")?.textContent ?? "#";
            const rawDate = item.querySelector("pubDate")?.textContent ?? "";
            const dateObj = rawDate ? new Date(rawDate) : null;
            return { title, link, rawDate, dateObj };
        });

        items = items
            .filter(i => i.dateObj instanceof Date && !isNaN(i.dateObj))
            .sort((a, b) => b.dateObj - a.dateObj)
            .slice(0, 10);

        // Mise à jour du compteur
        summary.textContent = `${label} (${items.length})`;

        if (items.length === 0) {
            details.classList.add("empty-source");
            return; // rien à afficher
        }

        // Affichage des articles
        items.forEach(item => {
            const div = document.createElement("div");
            div.className = "article-mini";
            const date = formatDateFR(item.rawDate);

            div.innerHTML = `
                <a href="${item.link}" target="_blank">${item.title}</a>
                <small>${date}</small>
            `;
            details.appendChild(div);
        });

    } catch (err) {
        console.error("Erreur flux local :", url, err);
        details.classList.add("empty-source");
    }
}


// ======================================================================
//  Chargement automatique via sources.json
// ======================================================================
async function loadLocalFeeds() {
    const response = await fetch("xml/sources.json");
    const sources = await response.json();

    const cleanLabel = (src) =>
        src.replace("xml/", "")
           .replace(".xml", "")
           .replace("rss_", "")
           .toUpperCase();

    sources.legal.forEach(src => {
        loadLocalRSS(src, "articles-legal", cleanLabel(src));
    });

    sources.pedago.forEach(src => {
        loadLocalRSS(src, "articles-pedago", cleanLabel(src));
    });

    sources.metiers.forEach(src => {
        loadLocalRSS(src, "articles-metiers", cleanLabel(src));
    });
}

document.addEventListener("DOMContentLoaded", loadLocalFeeds);
