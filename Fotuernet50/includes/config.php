<?php
// includes/config.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Base URL
define('APP_URL', "https://localhost/Gurukul_website/Fotuernet50");

// Email Configuration - अपने credentials से बदलें
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'arunkumarbind150720@gmail.com');
define('SMTP_PASSWORD', 'xfstffaxxmyazmjl'); // Google App Password
define('SMTP_FROM_EMAIL', 'arunkumarbind150720@gmail.com');
define('SMTP_FROM_NAME', 'Gurukul Website');
define('SMTP_SECURE', 'tls'); // tls or ssl
define('SMTP_DEBUG', 0); // Debug mode (0 = off)

date_default_timezone_set('Asia/Kolkata');
?>