window.addEventListener("load", () => {
    transformSections();
    loadAllSources();
});

function transformSections() {
    const sections = document.querySelectorAll("section.root-section");

    sections.forEach(section => {
        const code = section.dataset.section;
        const title = section.querySelector("h2").textContent;

        // Création du wrapper <details class="section-block">
        const wrapper = document.createElement("details");
        wrapper.classList.add("section-block");
        wrapper.open = true;
        wrapper.dataset.section = code;

        // Summary = ancien <h2>
        const summary = document.createElement("summary");
        summary.textContent = title;
        wrapper.appendChild(summary);

        // Bloc flux consolidé
        const flux = document.createElement("div");
        flux.classList.add("flux-consolidated");
        flux.innerHTML = `<a href="/docs/xml/flux_${code}.xml" target="_blank">flux_${code}.xml</a>`;
        wrapper.appendChild(flux);

        // Conteneur des flux individuels
        const container = document.createElement("div");
        container.classList.add("sources-container");
        wrapper.appendChild(container);

        // Remplacement du <section> par le <details>
        section.replaceWith(wrapper);
    });
}

async function loadAllSources() {
    try {
        const response = await fetch(BASE_URL + "/docs/xml/sources.php");
        const data = await response.json();

        Object.keys(data).forEach(sectionCode => {
            const container = document.querySelector(
                `.section-block[data-section="${sectionCode}"] .sources-container`
            );

            if (!container) return;

            data[sectionCode].forEach(rssPath => {
                createSourceBlock(container, rssPath);
            });
        });

    } catch (error) {
        console.error("Erreur lors du chargement des sources :", error);
    }
}

function createSourceBlock(container, rssPath) {

    const block = document.createElement("details");
    block.classList.add("article-group");
    block.open = true;

    const name = rssPath
        .replace("xml/rss_", "")
        .replace(".xml", "");

    const summary = document.createElement("summary");
    summary.textContent = `${name} (0)`;
    block.appendChild(summary);

    const articlesContainer = document.createElement("div");
    block.appendChild(articlesContainer);

    container.appendChild(block);

    loadRSS("/docs/" + rssPath, articlesContainer, summary);
}

async function loadRSS(url, container, summary) {
    try {
        const response = await fetch(url);
        const text = await response.text();
        parseRSS(text, container, summary);
    } catch (error) {
        container.innerHTML = `<p class="error">Impossible de charger le flux.</p>`;
    }
}

function parseRSS(xmlText, container, summary) {
    const parser = new DOMParser();
    const xml = parser.parseFromString(xmlText, "text/xml");

    let items = Array.from(xml.querySelectorAll("item"));

    items.sort((a, b) => {
        const da = new Date(a.querySelector("pubDate")?.textContent || 0);
        const db = new Date(b.querySelector("pubDate")?.textContent || 0);
        return db - da;
    });

    let count = 0;

    items.forEach(item => {
        const title = item.querySelector("title")?.textContent || "Sans titre";
        const link = item.querySelector("link")?.textContent || "#";
        const pubDate = item.querySelector("pubDate")?.textContent || "";

        const div = document.createElement("div");
        div.classList.add("article-mini");

        div.innerHTML = `
            <a href="${link}" target="_blank">${title}</a>
            <small>${formatDateTime(pubDate)}</small>
        `;

        container.appendChild(div);
        count++;
    });

// Limiter l'affichage à 20 articles
const MAX_VISIBLE = 10;

if (count > MAX_VISIBLE) {

    // Cacher les articles au-delà du 20e
    const hidden = Array.from(container.children).slice(MAX_VISIBLE);
    hidden.forEach(el => el.style.display = "none");

    // Créer le bouton "Voir plus"
    const btn = document.createElement("button");
    btn.classList.add("show-more-btn");
    btn.textContent = `Voir les ${count - MAX_VISIBLE} articles restants`;

    btn.addEventListener("click", () => {
        hidden.forEach(el => el.style.display = "");
        btn.remove();
    });

    container.appendChild(btn);
}

    summary.textContent = summary.textContent.replace("(0)", `(${count})`);
	if (count === 0) {
		summary.parentElement.classList.add("empty-source");
		summary.parentElement.open = false; // forcé fermé
	}


}

function formatDateTime(dateString) {
    if (!dateString) return "";
    const date = new Date(dateString);

    const jour = date.toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "short",
        year: "numeric"
    });

    const heure = date.toLocaleTimeString("fr-FR", {
        hour: "2-digit",
        minute: "2-digit"
    });

    return `${jour}, ${heure}`;
}
