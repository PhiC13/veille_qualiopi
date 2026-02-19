<?php
// ===============================================
// API LOGS — pipeline_logs + logs_sources
// ===============================================

require_once __DIR__ . "/../../../db_conn.php";
header("Content-Type: application/json; charset=utf-8");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

$pdo = get_pdo();

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
// PARAMÈTRES
// ===============================================
$type      = $_GET["type"]      ?? "pipeline"; // pipeline | sources
$source_id = $_GET["source_id"] ?? null;
$status    = $_GET["status"]    ?? null;
$limit     = intval($_GET["limit"]  ?? 100);
$offset    = intval($_GET["offset"] ?? 0);

// ===============================================
// TOTAL DES LOGS (mode count)
// ===============================================
if (isset($_GET['count'])) {

    if ($type === "pipeline") {
        $sql = "SELECT COUNT(*) AS total FROM pipeline_logs";
    } else {
        $sql = "SELECT COUNT(*) AS total FROM logs_sources";
    }

    $stmt = $pdo->query($sql);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    json_ok($res);
}

// ===============================================
// LOGS PIPELINE
// ===============================================
if ($type === "pipeline") {

    $sql = "
        SELECT id, run_id, timestamp, status, message, source_id
        FROM pipeline_logs
        WHERE 1
    ";

    $params = [];

    if ($source_id) {
        $sql .= " AND source_id = :source_id";
        $params[":source_id"] = intval($source_id);
    }

    if ($status) {
        $sql .= " AND status = :status";
        $params[":status"] = $status;
    }

    $sql .= " ORDER BY timestamp DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }

    $stmt->execute();
    json_ok($stmt->fetchAll());
}

// ===============================================
// LOGS SOURCES
// ===============================================
if ($type === "sources") {

    $sql = "
        SELECT id, source_id, statut, message, date
        FROM logs_sources
        WHERE 1
    ";

    $params = [];

    if ($source_id) {
        $sql .= " AND source_id = :source_id";
        $params[":source_id"] = intval($source_id);
    }

    if ($status) {
        $sql .= " AND statut = :status";
        $params[":status"] = $status;
    }

    $sql .= " ORDER BY date DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }

    $stmt->execute();
    json_ok($stmt->fetchAll());
}

json_error("Type de log inconnu", 400);
