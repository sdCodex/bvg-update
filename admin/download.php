<?php
// download.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized');
}

if (empty($_GET['file'])) {
    http_response_code(400);
    exit('Invalid request');
}

// sanitize and resolve path
$requested = $_GET['file']; // expected e.g. uploads/resume.pdf
// prevent directory traversal
$requested = str_replace(['..', "\0"], '', $requested);

$baseDir = realpath(__DIR__ . '/../uploads'); // uploads folder at project root
$filePath = realpath(__DIR__ . '/../' . $requested);

if (!$filePath || strpos($filePath, $baseDir) !== 0 || !file_exists($filePath)) {
    http_response_code(404);
    exit('File not found');
}

// Optionally force download if dl=1
$forceDownload = isset($_GET['dl']) && $_GET['dl'] == '1';

$mime = mime_content_type($filePath);
$filename = basename($filePath);

// Clear output buffers
if (ob_get_level()) ob_end_clean();

// Set headers
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
if ($forceDownload) {
    header('Content-Disposition: attachment; filename="' . rawurlencode($filename) . '"');
} else {
    header('Content-Disposition: inline; filename="' . rawurlencode($filename) . '"');
}
header('Content-Length: ' . filesize($filePath));
header('Pragma: public');
header('Cache-Control: must-revalidate');

// Read file
readfile($filePath);
exit;
