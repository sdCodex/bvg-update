<?php
include 'includes/db.php'; // PDO connection

// 1️⃣ Raw JSON lo
$rawData = file_get_contents("php://input");

// 2️⃣ Log banao (debug ke liye)
file_put_contents("callback.log", $rawData . PHP_EOL, FILE_APPEND);

// 3️⃣ JSON → Array
$data = json_decode($rawData, true);

if (!$data) {
    exit("Invalid callback data");
}

// 4️⃣ PhonePe data extract karo
$orderId = $data['data']['merchantOrderId'] ?? null;
$status  = $data['code'] ?? null; // PAYMENT_SUCCESS / PAYMENT_FAILED

if (!$orderId || !$status) {
    exit("Missing data");
}

// 5️⃣ DB update
$stmt = $pdo->prepare("
    UPDATE admissions 
    SET payment_status = ? 
    WHERE order_id = ?
");

$stmt->execute([$status, $orderId]);

echo "OK";
