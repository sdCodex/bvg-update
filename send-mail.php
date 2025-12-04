<?php
// send-mail.php (Root में)
require_once 'includes/config.php';
require_once 'includes/mail_config.php';

// Test email
$to = "arunkumarbind150720@gmail.com"; // अपना email डालें
$subject = "Test Email from Gurukul Website";
$body = "
<h1>Test Email</h1>
<p>This is a test email sent at: " . date('Y-m-d H:i:s') . "</p>
<p>If you received this, your email setup is working correctly!</p>
";

if (sendEmail($to, $subject, $body)) {
    echo "<h2 style='color: green;'>✓ Email sent successfully!</h2>";
    echo "<p>Check your inbox (and spam folder).</p>";
} else {
    echo "<h2 style='color: red;'>✗ Failed to send email</h2>";
    echo "<p>Check error logs for details.</p>";
}

echo "<hr>";
echo "<h3>Debug Info:</h3>";
echo "<p>SMTP Host: " . SMTP_HOST . "</p>";
echo "<p>SMTP Port: " . SMTP_PORT . "</p>";
echo "<p>From Email: " . SMTP_FROM_EMAIL . "</p>";
?>