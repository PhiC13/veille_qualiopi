<?php
// ===============================================
// API SECTIONS — CRUD complet (JSON)
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

// -------------------------
// GET — liste des sections
// -------------------------
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $stmt = $pdo->query("SELECT id, code, label, ordre FROM sections ORDER BY ordre ASC, id ASC");
    $sections = $stmt->fetchAll();

    json_ok($sections);
}


// -------------------------
// POST — créer une section
// -------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input || !isset($input["code"]) || !isset($input["label"])) {
        json_error("Champs manquants : code, label");
    }

    $code  = trim($input["code"]);
    $label = trim($input["label"]);
    $ordre = intval($input["ordre"] ?? 0);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO sections (code, label, ordre)
            VALUES (:code, :label, :ordre)
        ");
        $stmt->execute([
            ":code"  => $code,
            ":label" => $label,
            ":ordre" => $ordre
        ]);

        json_ok([
            "success" => true,
            "id" => $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {
        json_error("Erreur SQL : " . $e->getMessage(), 500);
    }
}


// -------------------------
// PUT — modifier une section
// -------------------------
if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    if (!isset($_GET["id"])) {
        json_error("ID manquant");
    }

    $id = intval($_GET["id"]);
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        json_error("JSON invalide");
    }

    $code  = trim($input["code"] ?? "");
    $label = trim($input["label"] ?? "");
    $ordre = intval($input["ordre"] ?? 0);

    try {
        $stmt = $pdo->prepare("
            UPDATE sections
            SET code = :code, label = :label, ordre = :ordre
            WHERE id = :id
        ");
        $stmt->execute([
            ":code"  => $code,
            ":label" => $label,
            ":ordre" => $ordre,
            ":id"    => $id
        ]);

        json_ok(["success" => true]);

    } catch (PDOException $e) {
        json_error("Erreur SQL : " . $e->getMessage(), 500);
    }
}


// -------------------------
// DELETE — supprimer une section
// -------------------------
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    if (!isset($_GET["id"])) {
        json_error("ID manquant");
    }

    $id = intval($_GET["id"]);

    try {
        $stmt = $pdo->prepare("DELETE FROM sections WHERE id = :id");
        $stmt->execute([":id" => $id]);

        json_ok(["success" => true]);

    } catch (PDOException $e) {
        json_error("Erreur SQL : " . $e->getMessage(), 500);
    }
}


// -------------------------
// Méthode non supportée
// -------------------------
json_error("Méthode non supportée", 405);
