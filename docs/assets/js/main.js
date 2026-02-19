console.log("MAIN.JS CHARGÉ");


// ------------------------------------------------------------
//  Formatage de la date en français
// ------------------------------------------------------------ 
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

// ------------------------------------------------------------
//  Recherche interne (toutes colonnes)
// ------------------------------------------------------------
function setupSearch() {
    const input = document.getElementById("searchInput");
    if (!input) return;

    input.addEventListener("input", function () {
        const query = this.value.toLowerCase();

        // Tous les articles du dashboard
        const articles = document.querySelectorAll(".article-mini, article");

        articles.forEach(article => {
            const text = article.innerText.toLowerCase();
            article.style.display = text.includes(query) ? "" : "none";
        });
    });
}

// ------------------------------------------------------------
//  Initialisation
// ------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    setupSearch();
});
