// ======================================================================
//  activity.js — Version neutre (graphique désactivé, zéro erreur)
// ======================================================================

// Placeholder pour un éventuel graphique futur
let activityChart = null;

// ======================================================================
//  Fonctions utilitaires (stubs)
// ======================================================================

function getDayKey(date) {
    return "";
}

function getWeekKey(date) {
    return "";
}

function filterByDays(entries, days) {
    return [];
}

// ======================================================================
//  Chargement de l'historique (désactivé)
// ======================================================================

async function loadActivityHistory() {
    console.log("loadActivityHistory() désactivé — aucun historique chargé.");
    return [];
}

// ======================================================================
//  Regroupements (désactivés)
// ======================================================================

function groupByDay(entries) {
    return {};
}

function groupByWeek(entries) {
    return {};
}

function groupByMonth(entries) {
    return {};
}

// ======================================================================
//  Rendu du graphique (désactivé)
// ======================================================================

function renderChart(labels, values, labelText) {
    console.log("renderChart() désactivé — aucun graphique affiché.");
}

// ======================================================================
//  Fonctions publiques (compatibles mais neutres)
// ======================================================================

async function updateActivityChartWithRange(days) {
    console.log("updateActivityChartWithRange() désactivé.");
}

async function updateWeeklyChart() {
    console.log("updateWeeklyChart() désactivé.");
}

async function updateMonthlyChart() {
    console.log("updateMonthlyChart() désactivé.");
}

// ======================================================================
//  Initialisation (ne fait rien, mais reste compatible)
// ======================================================================

document.addEventListener("DOMContentLoaded", () => {
    console.log("activity.js chargé (mode neutre).");
});
