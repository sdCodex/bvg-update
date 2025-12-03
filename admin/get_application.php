<?php
// get_application.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['error'=>'Unauthorized']);
    exit;
}

require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error'=>'Invalid id']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM job_applications WHERE id = ?");
    $stmt->execute([$id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$app) {
        http_response_code(404);
        echo json_encode(['error'=>'Not found']);
        exit;
    }

    // Return JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($app);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error'=>'Server error']);
    exit;
}
