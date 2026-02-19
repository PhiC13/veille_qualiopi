/* ============================================================
   ADMIN SECTIONS — Version harmonisée avec admin.css
============================================================ */

let SECTIONS = [];
let isEditing = false;
let editingId = null;

/* ------------------------------------------------------------
   INIT
------------------------------------------------------------ */
document.addEventListener("DOMContentLoaded", () => {
    loadSections();

    const form = document.getElementById("section-form");
    const cancelBtn = document.getElementById("cancel-edit");

    if (form) {
        form.addEventListener("submit", onSubmitForm);
    }
    if (cancelBtn) {
        cancelBtn.addEventListener("click", () => setFormMode(false));
    }
});

/* ------------------------------------------------------------
   API CALLS
------------------------------------------------------------ */
async function loadSections() {
    const res = await fetch("/docs/pages/api/sections.php");
    SECTIONS = await res.json();
    renderSections();
}

async function createSection(data) {
    return fetch("/docs/pages/api/sections.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    });
}

async function updateSection(id, data) {
    return fetch(`/docs/pages/api/sections.php?id=${id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    });
}

async function deleteSection(id) {
    return fetch(`/docs/pages/api/sections.php?id=${id}`, {
        method: "DELETE"
    });
}

/* ------------------------------------------------------------
   RENDU DES SECTIONS
------------------------------------------------------------ */
function renderSections() {
    const container = document.getElementById("sections-list");
    if (!container) return;

    container.innerHTML = "";

    SECTIONS.forEach(sec => {
        const row = document.createElement("section");
        row.className = "admin-row";

        row.innerHTML = `
            <div>
                <h3>${sec.label}</h3>
                <div class="meta">Code : ${sec.code}</div>
            </div>

            <div class="source-actions">
                <button class="btn-edit-admin" data-id="${sec.id}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                    </svg>
                    Modifier
                </button>

                <button class="btn-delete-admin" data-id="${sec.id}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14H6L5 6" />
                        <path d="M10 11v6" />
                        <path d="M14 11v6" />
                    </svg>
                    Supprimer
                </button>
            </div>
        `;

        container.appendChild(row);
    });

    attachListeners();
}

/* ------------------------------------------------------------
   LISTENERS
------------------------------------------------------------ */
function attachListeners() {
    document.querySelectorAll(".btn-edit-admin").forEach(btn => {
        btn.addEventListener("click", onEditSection);
    });

    document.querySelectorAll(".btn-delete-admin").forEach(btn => {
        btn.addEventListener("click", onDeleteSection);
    });
}

/* ------------------------------------------------------------
   FORMULAIRE — MODE AJOUT / EDITION
------------------------------------------------------------ */
function setFormMode(editMode) {
    isEditing = editMode;
    const title = document.getElementById("form-title");
    const cancelBtn = document.getElementById("cancel-edit");
    const form = document.getElementById("section-form");

    if (editMode) {
        if (title) title.textContent = "Modifier une section";
        if (cancelBtn) cancelBtn.style.display = "inline-block";
    } else {
        if (title) title.textContent = "Ajouter une section";
        if (cancelBtn) cancelBtn.style.display = "none";
        editingId = null;
        if (form) form.reset();
    }
}

/* ------------------------------------------------------------
   EDITION
------------------------------------------------------------ */
function onEditSection(e) {
    const btn = e.target.closest("button");
    if (!btn) return;

    const id = btn.dataset.id;
    editingId = id;

    const sec = SECTIONS.find(s => String(s.id) === String(id));
    if (!sec) return;

    document.getElementById("sec-code").value = sec.code;
    document.getElementById("sec-label").value = sec.label;
    document.getElementById("sec-ordre").value = sec.ordre;

    setFormMode(true);
}

/* ------------------------------------------------------------
   SUPPRESSION
------------------------------------------------------------ */
async function onDeleteSection(e) {
    const btn = e.target.closest("button");
    if (!btn) return;

    const id = btn.dataset.id;

    if (!confirm("Supprimer cette section ?\n⚠️ Toutes les sources associées seront supprimées.")) return;

    await deleteSection(id);
    await loadSections();
}

/* ------------------------------------------------------------
   SUBMIT FORMULAIRE
------------------------------------------------------------ */
async function onSubmitForm(e) {
    e.preventDefault();

    const data = {
        code: document.getElementById("sec-code").value.trim(),
        label: document.getElementById("sec-label").value.trim(),
        ordre: parseInt(document.getElementById("sec-ordre").value || 0, 10)
    };

    if (!data.code || !data.label) {
        alert("Veuillez remplir tous les champs obligatoires.");
        return;
    }

    if (isEditing) {
        await updateSection(editingId, data);
    } else {
        await createSection(data);
    }

    await loadSections();
    setFormMode(false);
}
