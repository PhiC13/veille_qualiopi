<?php
// ===============================================
// API SOURCES — CRUD complet (JSON)
// ===============================================

require_once __DIR__ . "/../../../db_conn.php";
header("Content-Type: application/json; charset=utf-8");

// Autoriser CORS local (optionnel)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Réponse OPTIONS (préflight)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

$pdo = get_pdo();

// ===============================================
// Helpers JSON
// ===============================================
function json_error($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(["error" => $msg]);
    exit;
}

function json_ok($data) {
    echo json_encode($data);
    exit;
}

// ===============================================
// ROUTES
// ===============================================


// --------------------------------------------------
// GET — liste des sources (groupées par section)
// --------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $sql = "
        SELECT 
            s.id,
            s.section_id,
            sec.code AS section_code,
            sec.label AS section_label,
            s.label,
            s.url,
            s.type,
            s.actif,
            s.last_checked,
            s.ordre,
            s.date_ajout,
            s.date_modif
        FROM sources s
        JOIN sections sec ON sec.id = s.section_id
        ORDER BY sec.ordre ASC, s.ordre ASC, s.id ASC
    ";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    // Regrouper par section
    $grouped = [];
    foreach ($rows as $row) {
        $sec = $row["section_code"];
        if (!isset($grouped[$sec])) {
            $grouped[$sec] = [
                "section_id" => $row["section_id"],
                "section_label" => $row["section_label"],
                "sources" => []
            ];
        }
        $grouped[$sec]["sources"][] = $row;
    }

    json_ok($grouped);
}


// --------------------------------------------------
// POST — créer une source
// --------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) json_error("JSON invalide");

    $required = ["section_id", "label", "url", "type"];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            json_error("Champ manquant : $field");
        }
    }

    $section_id = intval($input["section_id"]);
    $label      = trim($input["label"]);
    $url        = trim($input["url"]);
    $type       = $input["type"] === "local" ? "local" : "external";
    $actif      = intval($input["actif"] ?? 1);
    $ordre      = intval($input["ordre"] ?? 0);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO sources (section_id, label, url, type, actif, ordre)
            VALUES (:section_id, :label, :url, :type, :actif, :ordre)
        ");
        $stmt->execute([
            ":section_id" => $section_id,
            ":label"      => $label,
            ":url"        => $url,
            ":type"       => $type,
            ":actif"      => $actif,
            ":ordre"      => $ordre
        ]);

        json_ok([
            "success" => true,
            "id" => $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {
        json_error("Erreur SQL : " . $e->getMessage(), 500);
    }
}


// --------------------------------------------------
// PUT — modifier une source
// --------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    if (!isset($_GET["id"])) json_error("ID manquant");

    $id = intval($_GET["id"]);
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) json_error("JSON invalide");

    $section_id = intval($input["section_id"] ?? 0);
    $label      = trim($input["label"] ?? "");
    $url        = trim($input["url"] ?? "");
    $type       = ($input["type"] ?? "external") === "local" ? "local" : "external";
    $actif      = intval($input["actif"] ?? 1);
    $ordre      = intval($input["ordre"] ?? 0);

    try {
        $stmt = $pdo->prepare("
            UPDATE sources
            SET section_id = :section_id,
                label = :label,
                url = :url,
                type = :type,
                actif = :actif,
                ordre = :ordre
            WHERE id = :id
        ");
        $stmt->execute([
            ":section_id" => $section_id,
            ":label"      => $label,
            ":url"        => $url,
            ":type"       => $type,
            ":actif"      => $actif,
            ":ordre"      => $ordre,
            ":id"         => $id
        ]);

        json_ok(["success" => true]);

    } catch (PDOException $e) {
        json_error("Erreur SQL : " . $e->getMessage(), 500);
    }
}


// --------------------------------------------------
// DELETE — supprimer une source
// --------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    if (!isset($_GET["id"])) json_error("ID manquant");

    $id = intval($_GET["id"]);

    try {
        $stmt = $pdo->prepare("DELETE FROM sources WHERE id = :id");
        $stmt->execute([":id" => $id]);

        json_ok(["success" => true]);

    } catch (PDOException $e) {
        json_error("Erreur SQL : " . $e->getMessage(), 500);
    }
}


// --------------------------------------------------
// Méthode non supportée
// --------------------------------------------------
json_error("Méthode non supportée", 405);
