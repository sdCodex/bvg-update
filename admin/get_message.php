<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once '../includes/db.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($message) {
            header('Content-Type: application/json');
            echo json_encode($message);
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Message not found']);
        }
    } catch (PDOException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Database error']);
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Message ID required']);
}
?>