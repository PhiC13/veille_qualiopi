<?php
// ===============================================
// API PIPELINE — logs par run_id
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
// TOTAL DES RUNS (mode count)
// ===============================================
if (isset($_GET['count'])) {
    $sql = "SELECT COUNT(DISTINCT run_id) AS total FROM pipeline_logs";
    $stmt = $pdo->query($sql);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    json_ok($res);
}

// ===============================================
// PARAMÈTRES
// ===============================================
$run_id = $_GET["run_id"] ?? null;
$limit  = intval($_GET["limit"]  ?? 50);
$offset = intval($_GET["offset"] ?? 0);

// ===============================================
// MODE 1 : LISTE DES RUNS
// ===============================================
if (!$run_id) {

    $sql = "
        SELECT run_id, MIN(timestamp) AS started_at, MAX(timestamp) AS ended_at,
               COUNT(*) AS events,
               SUM(status = 'error') AS errors
        FROM pipeline_logs
        GROUP BY run_id
        ORDER BY run_id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    json_ok($stmt->fetchAll());
}

// ===============================================
// MODE 2 : DÉTAIL D’UN RUN
// ===============================================
$sql = "
    SELECT id, timestamp, status, message, source_id
    FROM pipeline_logs
    WHERE run_id = :run_id
    ORDER BY timestamp ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":run_id" => intval($run_id)]);

json_ok($stmt->fetchAll());
