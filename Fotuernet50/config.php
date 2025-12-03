<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORRECT APP_URL (without /package)
define('APP_URL', "https://localhost/Gurukul_website/Fotuernet50");

// CORRECT RESPONSE PATH
define('RESPONSE_PATH', APP_URL . "/package/response.php");

date_default_timezone_set('Asia/Kolkata');

// Environment and configuration
define('ENV', "TEST"); // PROD or TEST
define('CLIENT_ID', "");
define('CLIENT_SECRET', "f");
define('CLIENT_VERSION', "1");

// Email configuration for PHPMailer
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'your-email@gmail.com');
define('SMTP_FROM_NAME', 'Gurukul Website');
define('SMTP_SECURE', 'tls');
define('SMTP_AUTH', true);
define('SMTP_DEBUG', 0); // 0 = off, 1 = client messages, 2 = client and server messages
?>