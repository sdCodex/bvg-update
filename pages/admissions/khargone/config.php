<?php
// config.php

// Base URL
$base_url = '/Gurkul_website';

// PhonePe Configuration
define('CLIENT_ID', 'YOUR_CLIENT_ID');
define('CLIENT_SECRET', 'YOUR_CLIENT_SECRET');
define('CLIENT_VERSION', '1.0');
define('ENV', 'SANDBOX'); // or 'PRODUCTION'

// Response/Callback URLs
define('RESPONSE_PATH', $base_url . '/pages/admissions/khargone/payment-response.php');
?>