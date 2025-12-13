<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/phonepe-config.php';

use PhonePe\Payments\v2\StandardCheckout\StandardCheckoutClient;

$phonepeClient = StandardCheckoutClient::getInstance(
    PHONEPE_CLIENT_ID,
    PHONEPE_CLIENT_VERSION,
    PHONEPE_CLIENT_SECRET,
    PHONEPE_ENV
);
