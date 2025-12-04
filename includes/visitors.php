<?php

$dbPaths = [
    __DIR__ . "/db.php",
    __DIR__ . "/../db.php",
    $_SERVER['DOCUMENT_ROOT'] . "/db.php",
    $_SERVER['DOCUMENT_ROOT'] . "/includes/db.php"
];

foreach ($dbPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

if (!isset($pdo)) {
    die("❌ Database connection not found. Please check db.php path.");
}

// Get Visitor IP & Today's Date
$ip = $_SERVER['REMOTE_ADDR'];
$today = date("Y-m-d");

// Check if visitor already recorded today
$checkQuery = $pdo->prepare("SELECT id FROM visitors WHERE ip_address = ? AND visit_date = ?");
$checkQuery->execute([$ip, $today]);
$exists = $checkQuery->fetch();

if (!$exists) {
    $insertQuery = $pdo->prepare("INSERT INTO visitors (ip_address, visit_date) VALUES (?, ?)");
    $insertQuery->execute([$ip, $today]);
}

// Fetch Total Visitors
$totalResult = $pdo->query("SELECT COUNT(*) AS total FROM visitors");
$total = $totalResult->fetch()['total'];

// Artificially increase total (without affecting today's count)
$total += 500; // yahan 100 jitna bhi extra dikhana ho add kar sakte ho

// Fetch Today's Visitors
$todayQuery = $pdo->prepare("SELECT COUNT(*) AS today FROM visitors WHERE visit_date=?");
$todayQuery->execute([$today]);
$todayCount = $todayQuery->fetch()['today'];

// Format numbers Indian style
function formatIndian($num) {
    return number_format($num, 0, '', ',');
}
?>
