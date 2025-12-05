<?php
// includes/config.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Base URL
define('APP_URL', "https://localhost/Gurukul_website/Fotuernet50");

// config.php में यह जोड़ें
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_PORT', 587);
define('ADMIN_EMAIL', 'admin@fortunate51.edu.in');

date_default_timezone_set('Asia/Kolkata');
?>