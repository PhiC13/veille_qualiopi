/* ============================================================
   ADMIN LOGS — Version SQL moderne
============================================================ */

let LOGS = [];
let TOTAL_LOGS = 0;
let currentType = "pipeline";
let currentPage = 0;
const PAGE_SIZE = 50;

/* ------------------------------------------------------------
   INIT
------------------------------------------------------------ */
document.addEventListener("DOMContentLoaded", () => {
    loadLogs();
    document.getElementById("log-type").addEventListener("change", onTypeChange);
    document.getElementById("log-next").addEventListener("click", () => changePage(1));
    document.getElementById("log-prev").addEventListener("click", () => changePage(-1));
});

/* ------------------------------------------------------------
   API CALLS
------------------------------------------------------------ */
async function loadTotalLogs() {
    const res = await fetch(`/docs/pages/api/logs.php?count=1&type=${currentType}`);
    const data = await res.json();
    TOTAL_LOGS = parseInt(data.total);
}

async function loadLogs() {
    await loadTotalLogs();

    const url = `/docs/pages/api/logs.php?type=${currentType}&limit=${PAGE_SIZE}&offset=${currentPage * PAGE_SIZE}`;
    const res = await fetch(url);
    LOGS = await res.json();

    renderLogs();
 	updateTotalLogs();
    updatePaginationButtons();
    renderPagination();
}

/* ------------------------------------------------------------
   RENDU
------------------------------------------------------------ */
function renderLogs() {
    const container = document.getElementById("logs-list");
    container.innerHTML = "";

    LOGS.forEach(log => {
        const row = document.createElement("section");
        row.className = "admin-row";

        let status = currentType === "pipeline" ? log.status : log.statut;
        const s = (status || "").toLowerCase();

        let statusClass = "info";
        if (s.includes("ok")) statusClass = "ok";
        else if (s.includes("error") || s.includes("err")) statusClass = "error";
        else if (s.includes("warn")) statusClass = "warning";

        row.innerHTML = `
            <div>
                <h3>${currentType === "pipeline" ? log.timestamp : log.date}</h3>
                <div class="meta">${log.message}</div>
            </div>

            <div class="log-status ${statusClass}">
                ${status}
            </div>
        `;

        container.appendChild(row);
    });
}

/* ------------------------------------------------------------
   FILTRES
------------------------------------------------------------ */
function onTypeChange(e) {
    currentType = e.target.value;
    currentPage = 0;
    loadLogs();
}

/* ------------------------------------------------------------
   PAGINATION — Précédent / Suivant
------------------------------------------------------------ */
function changePage(delta) {
    if (currentPage + delta < 0) return;
    if ((currentPage + delta) * PAGE_SIZE >= TOTAL_LOGS) return;

    currentPage += delta;
    loadLogs();
}

function updatePaginationButtons() {
    const btnPrev = document.getElementById("log-prev");
    const btnNext = document.getElementById("log-next");

    btnPrev.style.display = currentPage === 0 ? "none" : "inline-block";
    btnNext.style.display = (currentPage + 1) * PAGE_SIZE >= TOTAL_LOGS ? "none" : "inline-block";
}

/* ------------------------------------------------------------
   PAGINATION NUMÉROTÉE COMPACTE
------------------------------------------------------------ */
function renderPagination() {
    const container = document.getElementById("logs-pagination");
    if (!container) return;

    container.innerHTML = "";

    const totalPages = Math.ceil(TOTAL_LOGS / PAGE_SIZE);

    function addButton(label, page, active = false) {
        const btn = document.createElement("button");
        btn.textContent = label;
        if (active) btn.classList.add("active");
        btn.addEventListener("click", () => {
            currentPage = page;
            loadLogs();
        });
        container.appendChild(btn);
    }

    function addEllipsis() {
        const span = document.createElement("span");
        span.textContent = "…";
        span.style.opacity = "0.6";
        container.appendChild(span);
    }

    // Page 1
    addButton(1, 0, currentPage === 0);

    // Ellipse gauche
    if (currentPage > 2) addEllipsis();

    // Pages autour
    for (let p = currentPage - 1; p <= currentPage + 1; p++) {
        if (p > 0 && p < totalPages - 1) {
            addButton(p + 1, p, p === currentPage);
        }
    }

    // Ellipse droite
    if (currentPage < totalPages - 3) addEllipsis();

    // Dernière page
    if (totalPages > 1) {
        addButton(totalPages, totalPages - 1, currentPage === totalPages - 1);
    }
}

function updateTotalLogs() {
    const el = document.getElementById("logs-total");
    el.textContent = `(${TOTAL_LOGS} logs)`;
}
