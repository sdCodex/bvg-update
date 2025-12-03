<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

/**
 * Send Admin Notification Email for Payment
 */
function sendAdminMail($name, $email, $phone, $amount, $transactionId) {
    $mail = new PHPMailer(true);

    try {
        // SMTP SETTINGS - APNE CREDENTIALS DAALEIN
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cmd@ourgurukul.org'; // Your Gmail
        $mail->Password   = 'swdrepfqffddfjuk'; // Your App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->Timeout    = 30;
        $mail->CharSet    = 'UTF-8';

        // SENDER
        $mail->setFrom('cmd@ourgurukul.org', 'Fortunate 51 Payment Bot');

        // RECEIVER (Admin)
        $mail->addAddress('info@ourgurukul.org', 'Fortunate 51 Admin');

        // STUDENT KO BHI BCC
        $mail->addBCC($email, $name);

        // EMAIL CONTENT
        $mail->isHTML(true);
        $mail->Subject = "New Payment Received - $name";
        
        $mail->Body = createAdminEmailTemplate($name, $email, $phone, $amount, $transactionId);
        $mail->AltBody = createAdminPlainTextEmail($name, $email, $phone, $amount, $transactionId);

        if ($mail->send()) {
            error_log("✅ ADMIN NOTIFICATION EMAIL SENT SUCCESSFULLY");
            return true;
        } else {
            error_log("❌ ADMIN EMAIL SEND FAILED: " . $mail->ErrorInfo);
            return false;
        }

    } catch (Exception $e) {
        error_log("💥 ADMIN EMAIL EXCEPTION: " . $e->getMessage());
        return false;
    }
}

/**
 * Create HTML Email Template for Admin
 */
function createAdminEmailTemplate($name, $email, $phone, $amount, $transactionId) {
    $payment_date = date('d/m/Y h:i A');
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #ddd; }
            .header { background: #7a0f0f; color: white; padding: 20px; text-align: center; }
            .content { padding: 25px; background: #f9f9f9; }
            .details { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
            table { width: 100%; border-collapse: collapse; }
            table td { padding: 8px 12px; border-bottom: 1px solid #eee; }
            .highlight { background: #f8f9fa; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>BHAKTIVEDANTA GURUKUL</h1>
                <h2>FORTUNATE 51 - New Payment Received</h2>
            </div>
            
            <div class='content'>
                <h3>💰 NEW PAYMENT RECEIVED</h3>
                <p>Dear Admin,</p>
                <p>A new payment has been successfully processed for the Fortunate 51 Scholarship.</p>
                
                <div class='details'>
                    <h3>Payment Details:</h3>
                    <table>
                        <tr>
                            <td class='highlight'>Student Name:</td>
                            <td>{$name}</td>
                        </tr>
                        <tr>
                            <td class='highlight'>Email:</td>
                            <td>{$email}</td>
                        </tr>
                        <tr>
                            <td class='highlight'>Phone:</td>
                            <td>{$phone}</td>
                        </tr>
                        <tr>
                            <td class='highlight'>Amount:</td>
                            <td>₹{$amount}</td>
                        </tr>
                        <tr>
                            <td class='highlight'>Transaction ID:</td>
                            <td>{$transactionId}</td>
                        </tr>
                        <tr>
                            <td class='highlight'>Payment Date:</td>
                            <td>{$payment_date}</td>
                        </tr>
                    </table>
                </div>
                
                <p><em>This is an automated notification. Please do not reply.</em></p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Create Plain Text Email for Admin
 */
function createAdminPlainTextEmail($name, $email, $phone, $amount, $transactionId) {
    $payment_date = date('d/m/Y h:i A');
    
    return "
FORTUNATE 51 SCHOLARSHIP - NEW PAYMENT RECEIVED
==============================================

NEW PAYMENT RECEIVED
Student Name: {$name}
Email: {$email}
Phone: {$phone}
Amount: ₹{$amount}
Transaction ID: {$transactionId}
Payment Date: {$payment_date}

This is an automated notification. Please do not reply.
    ";
}

/**
 * Standalone function to test email sending
 */
function testEmailSending() {
    // Test data
    $name = "Test Student";
    $email = "student@example.com";
    $phone = "9876543210";
    $amount = "500";
    $transactionId = "TEST_TXN_12345";
    
    $result = sendAdminMail($name, $email, $phone, $amount, $transactionId);
    
    if ($result) {
        echo "Test email sent successfully!";
    } else {
        echo "Test email failed!";
    }
}

// Agar directly access kiya hai to test run karo
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    testEmailSending();
}
?>