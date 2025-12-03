<?php
// send_email.php
require_once 'config.php'; // आपका मूल configuration file
require_once 'mail_config.php'; // PHPMailer configuration

// Email भेजने का example
$to = "recipient@example.com";
$subject = "Test Email from Gurukul Website";
$htmlContent = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 10px; text-align: center; }
        .content { padding: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Gurukul Website</h1>
        </div>
        <div class='content'>
            <h2>Hello User,</h2>
            <p>This is a test email from Gurukul Website.</p>
            <p>Sent at: " . date('Y-m-d H:i:s') . "</p>
        </div>
    </div>
</body>
</html>";

// Send email
if(sendEmail($to, $subject, $htmlContent)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email. Check error logs.";
}
?>