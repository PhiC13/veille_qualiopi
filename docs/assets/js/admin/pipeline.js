/* ============================================================
   ADMIN PIPELINE — Version SQL moderne (chevron + résumé inline)
============================================================ */

let RUNS = [];
let DETAILS = [];
let TOTAL_RUNS = 0;
let currentPage = 0;
const PAGE_SIZE = 20;

/* ------------------------------------------------------------
   INIT
------------------------------------------------------------ */
document.addEventListener("DOMContentLoaded", () => {
    loadRuns();
    document.getElementById("pipe-next").addEventListener("click", () => changePage(1));
    document.getElementById("pipe-prev").addEventListener("click", () => changePage(-1));
});

/* ------------------------------------------------------------
   API CALLS
------------------------------------------------------------ */
async function loadTotalRuns() {
    const res = await fetch(`/docs/pages/api/pipeline.php?count=1`);
    const data = await res.json();
    TOTAL_RUNS = parseInt(data.total);
}

async function loadRuns() {
    await loadTotalRuns();

    const url = `/docs/pages/api/pipeline.php?limit=${PAGE_SIZE}&offset=${currentPage * PAGE_SIZE}`;
    const res = await fetch(url);
    RUNS = await res.json();

    renderRuns();
    updateTotalRuns();
    updatePaginationButtons();
    renderPagination();
}

async function loadRunDetails(run_id) {
    const res = await fetch(`/docs/pages/api/pipeline.php?run_id=${run_id}`);
    DETAILS = await res.json();
    return DETAILS;
}

/* ------------------------------------------------------------
   RENDU — LISTE DES RUNS
------------------------------------------------------------ */
function renderRuns() {
    const container = document.getElementById("pipeline-list");
    container.innerHTML = "";

    RUNS.forEach(async run => {

        const row = document.createElement("div");
        row.className = "admin-row pipeline-row";
        row.dataset.run = run.run_id;

        const hasErrors = run.errors > 0;
        const status = hasErrors ? "ERROR" : "OK";
        const statusClass = hasErrors ? "error" : "ok";

        row.innerHTML = `
            <div class="pipe-left">
                <span class="pipe-chevron">▶</span>

                <div class="pipe-main">
                    <div class="pipe-title-line">
                        <h3>Run #${run.run_id}</h3>
                        <div class="pipe-summary-inline"></div>
                    </div>
                    <div class="pipe-run-dates meta">
                        ${run.started_at} → ${run.ended_at}
                    </div>
                </div>
            </div>

            <div class="pipe-status ${statusClass}">
                ${status}
            </div>
        `;

        container.appendChild(row);

        const details = await loadRunDetails(run.run_id);
        const summary = computeRunSummary(details);

        const inlineSummary = row.querySelector(".pipe-summary-inline");
        inlineSummary.innerHTML = `
            <span>${summary.total_flux} flux</span>
            <span class="ok">${summary.flux_ok} OK</span>
            <span class="error">${summary.flux_error} erreurs</span>
            <span class="import">${summary.total_imported} articles</span>
        `;

        const detail = document.createElement("div");
        detail.className = "admin-detail";
        detail.style.display = "none";
        detail.id = "detail-" + run.run_id;
        detail.innerHTML = `<div class="meta">Chargement…</div>`;

        row.insertAdjacentElement("afterend", detail);

        row.addEventListener("click", () => toggleDetails(run.run_id));
    });
}

/* ------------------------------------------------------------
   TOGGLE DÉTAILS
------------------------------------------------------------ */
async function toggleDetails(run_id) {
    const detail = document.getElementById("detail-" + run_id);
    const row = document.querySelector(`[data-run="${run_id}"]`);
    const chevron = row.querySelector(".pipe-chevron");

    if (detail.style.display === "none") {
        detail.style.display = "block";
        chevron.classList.add("open");

        if (!detail.dataset.loaded) {
            const details = await loadRunDetails(run_id);
            const summary = computeRunSummary(details);

            detail.innerHTML = `
                <div class="pipe-summary">
                    <span>${summary.total_flux} flux</span>
                    <span class="ok">${summary.flux_ok} OK</span>
                    <span class="error">${summary.flux_error} erreurs</span>
                    <span class="import">${summary.total_imported} articles</span>
                </div>
                <hr>
            `;

            details.forEach(log => {
                const msg = log?.message || "";
                const s = (log?.status || "").toLowerCase();

                let statusClass = "info";
                if (s.includes("ok")) statusClass = "ok";
                else if (s.includes("error")) statusClass = "error";
                else if (s.includes("warn")) statusClass = "warning";

                detail.innerHTML += `
                    <div class="meta">
                        <strong>${log?.timestamp || ""}</strong> —
                        <span class="pipe-status ${statusClass}">${log?.status || ""}</span><br>
                        ${msg}
                    </div>
                `;
            });

            detail.dataset.loaded = "1";
        }
    } else {
        detail.style.display = "none";
        chevron.classList.remove("open");
    }
}

/* ------------------------------------------------------------
   PAGINATION — Précédent / Suivant
------------------------------------------------------------ */
function changePage(delta) {
    if (currentPage + delta < 0) return;
    if ((currentPage + delta) * PAGE_SIZE >= TOTAL_RUNS) return;

    currentPage += delta;
    loadRuns();
}

function updatePaginationButtons() {
    const btnPrev = document.getElementById("pipe-prev");
    const btnNext = document.getElementById("pipe-next");

    btnPrev.style.display = currentPage === 0 ? "none" : "inline-block";
    btnNext.style.display = (currentPage + 1) * PAGE_SIZE >= TOTAL_RUNS ? "none" : "inline-block";
}

/* ------------------------------------------------------------
   PAGINATION — compacte “pro”
------------------------------------------------------------ */
function renderPagination() {
    const container = document.getElementById("pipeline-pagination");
    if (!container) return;

    container.innerHTML = "";

    const totalPages = Math.ceil(TOTAL_RUNS / PAGE_SIZE);

    function addButton(label, page, active = false) {
        const btn = document.createElement("button");
        btn.textContent = label;
        if (active) btn.classList.add("active");
        btn.addEventListener("click", () => {
            currentPage = page;
            loadRuns();
        });
        container.appendChild(btn);
    }

    function addEllipsis() {
        const span = document.createElement("span");
        span.textContent = "…";
        span.style.opacity = "0.6";
        container.appendChild(span);
    }

    addButton(1, 0, currentPage === 0);

    if (currentPage > 2) addEllipsis();

    for (let p = currentPage - 1; p <= currentPage + 1; p++) {
        if (p > 0 && p < totalPages - 1) {
            addButton(p + 1, p, p === currentPage);
        }
    }

    if (currentPage < totalPages - 3) addEllipsis();

    if (totalPages > 1) {
        addButton(totalPages, totalPages - 1, currentPage === totalPages - 1);
    }
}

/* ------------------------------------------------------------
   SYNTHÈSE D’UN RUN — version robuste
------------------------------------------------------------ */
function computeRunSummary(details) {
    let total_flux = 0;
    let flux_ok = 0;
    let flux_error = 0;
    let total_imported = 0;

    let current_flux = null;
    let flux_status = {};

    details.forEach(log => {
        if (!log || !log.message || typeof log.message !== "string") {
            return;
        }

        const msg = log.message;

        let m;
        if ((m = msg.match(/Lecture du flux : .* \(Source (\d+)\)/))) {
            current_flux = "SRC_" + m[1];
            flux_status[current_flux] = "PENDING";
            total_flux++;
            return;
        }

        if (msg.startsWith("Lecture du flux : COSMOS")) {
            current_flux = "COSMOS";
            flux_status[current_flux] = "PENDING";
            total_flux++;
            return;
        }

        if (log.status === "ERROR") {
            if (current_flux) flux_status[current_flux] = "ERROR";
            return;
        }

        if (log.status === "OK" && msg.match(/Source \d+ : .* lus, .* nouveaux/)) {
            if (current_flux && flux_status[current_flux] !== "ERROR") {
                flux_status[current_flux] = "OK";
            }
            return;
        }

        if (log.status === "OK" && msg.startsWith("COSMOS :")) {
            flux_status["COSMOS"] = "OK";
            return;
        }

        if (log.status === "OK" && msg.includes("articles importés")) {
            const m2 = msg.match(/(\d+) articles/);
            if (m2) total_imported = parseInt(m2[1]);
        }
    });

    flux_ok = Object.values(flux_status).filter(s => s === "OK").length;
    flux_error = Object.values(flux_status).filter(s => s === "ERROR").length;

    return { total_flux, flux_ok, flux_error, total_imported };
}

/* ------------------------------------------------------------
   TOTAL RUNS
------------------------------------------------------------ */
function updateTotalRuns() {
    const el = document.getElementById("pipeline-total");
    el.textContent = `(${TOTAL_RUNS} runs)`;
}
