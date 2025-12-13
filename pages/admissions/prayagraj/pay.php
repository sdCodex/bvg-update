<?php
require_once __DIR__ . '/phonepe-client.php';

$orderId = "ORD" . time();

$request = [
    "merchantOrderId" => $orderId,
    "amount" => 1000, // ₹10 = 1000 (paise)
    "redirectUrl" => "http://localhost/Gurkul_website/pages/admissions/prayagraj/success.php",
    "callbackUrl" => "http://localhost/Gurkul_website/pages/admissions/prayagraj/callback.php",
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
