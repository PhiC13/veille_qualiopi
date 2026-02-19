/* ============================================================
   ADMIN SOURCES — Liste plate par section (harmonisée)
============================================================ */

let SECTIONS = [];
let SOURCES = {};
let isEditing = false;
let editingId = null;

/* ------------------------------------------------------------
   INIT
------------------------------------------------------------ */
document.addEventListener("DOMContentLoaded", () => {
    loadAll();

    document.getElementById("source-form").addEventListener("submit", onSubmitForm);
    document.getElementById("cancel-edit").addEventListener("click", () => setFormMode(false));
});

/* ------------------------------------------------------------
   CHARGEMENT GLOBAL
------------------------------------------------------------ */
async function loadAll() {
    await loadSections();
    await loadSources();
    populateSectionSelect();
    renderSourcesFlat();
}

/* ------------------------------------------------------------
   API CALLS
------------------------------------------------------------ */
async function loadSections() {
    const res = await fetch("/docs/pages/api/sections.php");
    SECTIONS = await res.json();
}

async function loadSources() {
    const res = await fetch("/docs/pages/api/sources.php");
    SOURCES = await res.json();
}

async function createSource(data) {
    return fetch("/docs/pages/api/sources.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    });
}

async function updateSource(id, data) {
    return fetch(`/docs/pages/api/sources.php?id=${id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    });
}

async function deleteSource(id) {
    return fetch(`/docs/pages/api/sources.php?id=${id}`, {
        method: "DELETE"
    });
}

/* ------------------------------------------------------------
   RENDU — LISTE PLATE PAR SECTION (avec carte englobante)
------------------------------------------------------------ */
function renderSourcesFlat() {
    const container = document.getElementById("sources-list");
    container.innerHTML = "";

    SECTIONS.forEach(section => {
        const secCode = section.code;
        const secLabel = section.label;
        const list = SOURCES[secCode]?.sources || [];

        /* Carte englobante */
        const card = document.createElement("section");
        card.className = "admin-section-card";

        card.innerHTML = `
            <h3>${secLabel}</h3>
            <div class="admin-list"></div>
        `;

        const listContainer = card.querySelector(".admin-list");

        /* Lignes : sources */
        list.forEach(src => {
            const row = document.createElement("section");
            row.className = "admin-row";

            row.innerHTML = `
                <div>
                    <h3>${src.label}</h3>
                    <div class="meta">${src.url}</div>
                </div>

                <div class="actions">
                    <button class="btn-edit-admin" data-id="${src.id}">Modifier</button>
                    <button class="btn-delete-admin" data-id="${src.id}">Supprimer</button>
                </div>
            `;

            listContainer.appendChild(row);
        });

        container.appendChild(card);
    });

    attachListeners();
}

/* ------------------------------------------------------------
   LISTENERS
------------------------------------------------------------ */
function attachListeners() {
    document.querySelectorAll(".btn-edit-admin").forEach(btn =>
        btn.addEventListener("click", onEditSource)
    );

    document.querySelectorAll(".btn-delete-admin").forEach(btn =>
        btn.addEventListener("click", onDeleteSource)
    );
}

/* ------------------------------------------------------------
   FORMULAIRE — MODE AJOUT / EDITION
------------------------------------------------------------ */
function setFormMode(editMode) {
    isEditing = editMode;

    const title = document.getElementById("form-title");
    const cancelBtn = document.getElementById("cancel-edit");
    const form = document.getElementById("source-form");

    if (editMode) {
        title.textContent = "Modifier une source";
        cancelBtn.style.display = "inline-block";
    } else {
        title.textContent = "Ajouter une source";
        cancelBtn.style.display = "none";
        editingId = null;
        form.reset();
    }
}

/* ------------------------------------------------------------
   REMPLIR LE SELECT DES SECTIONS
------------------------------------------------------------ */
function populateSectionSelect() {
    const select = document.getElementById("src-section");
    select.innerHTML = `<option value="">— Choisir une section —</option>`;

    SECTIONS.forEach(sec => {
        const opt = document.createElement("option");
        opt.value = sec.id;
        opt.textContent = sec.label;
        select.appendChild(opt);
    });
}

/* ------------------------------------------------------------
   EDITION
------------------------------------------------------------ */
function onEditSource(e) {
    const id = e.target.dataset.id;
    editingId = id;

    let src = null;
    for (const sec of Object.values(SOURCES)) {
        src = sec.sources.find(s => s.id == id);
        if (src) break;
    }

    if (!src) return;

    document.getElementById("src-section").value = src.section_id;
    document.getElementById("src-label").value = src.label;
    document.getElementById("src-url").value = src.url;
    document.getElementById("src-type").value = src.type;
    document.getElementById("src-actif").checked = src.actif == 1;
    document.getElementById("src-ordre").value = src.ordre;

    setFormMode(true);
}

/* ------------------------------------------------------------
   SUPPRESSION
------------------------------------------------------------ */
async function onDeleteSource(e) {
    const id = e.target.dataset.id;

    if (!confirm("Supprimer cette source ?")) return;

    await deleteSource(id);
    await loadAll();
}

/* ------------------------------------------------------------
   SUBMIT FORMULAIRE
------------------------------------------------------------ */
async function onSubmitForm(e) {
    e.preventDefault();

    const data = {
        section_id: parseInt(document.getElementById("src-section").value),
        label: document.getElementById("src-label").value.trim(),
        url: document.getElementById("src-url").value.trim(),
        type: document.getElementById("src-type").value,
        actif: document.getElementById("src-actif").checked ? 1 : 0,
        ordre: parseInt(document.getElementById("src-ordre").value || 0)
    };

    if (!data.section_id || !data.label || !data.url) {
        alert("Veuillez remplir tous les champs obligatoires.");
        return;
    }

    if (isEditing) {
        await updateSource(editingId, data);
    } else {
        await createSource(data);
    }

    await loadAll();
    setFormMode(false);
}
