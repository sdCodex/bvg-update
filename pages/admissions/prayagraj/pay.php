<?php
require_once __DIR__ . '/phonepe-client.php';

$orderId = "ORD" . time();

$request = [
    "merchantOrderId" => $orderId,
    "amount" => 1000, // ₹10 = 1000 (paise)
    "redirectUrl" => "http://localhost/project/success.php",
    "callbackUrl" => "http://localhost/project/callback.php",
    "paymentInstrument" => [
        "type" => "PAY_PAGE"
    ]
];

$response = $phonepeClient->initiatePayment($request);

if ($response->isSuccess()) {
    header("Location: " . $response->getRedirectUrl());
    exit;
}

echo "Payment initiation failed";
