<?php
// includes/mail_config.php

// PHPMailer files include
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send Email Function
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML or plain)
 * @param string $cc CC emails (optional)
 * @param string $bcc BCC emails (optional)
 * @param array $attachments File paths array (optional)
 * @return bool Success status
 */
function sendEmail($to, $subject, $body, $cc = '', $bcc = '', $attachments = []) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->SMTPDebug  = SMTP_DEBUG;
        
        // Enable verbose debug output if needed
        if (SMTP_DEBUG > 0) {
            $mail->SMTPDebug = SMTP_DEBUG;
        }
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Handle multiple recipients
        if (is_array($to)) {
            foreach ($to as $email) {
                $mail->addAddress($email);
            }
        } else {
            $mail->addAddress($to);
        }
        
        // Add CC
        if (!empty($cc)) {
            if (is_array($cc)) {
                foreach ($cc as $email) {
                    $mail->addCC($email);
                }
            } else {
                $mail->addCC($cc);
            }
        }
        
        // Add BCC
        if (!empty($bcc)) {
            if (is_array($bcc)) {
                foreach ($bcc as $email) {
                    $mail->addBCC($email);
                }
            } else {
                $mail->addBCC($bcc);
            }
        }
        
        // Attachments
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $mail->addAttachment($attachment);
                }
            }
        }
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        // Plain text version for non-HTML clients
        $mail->AltBody = strip_tags($body);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log error
        error_log("Mailer Error: " . $mail->ErrorInfo);
        
        // For debugging, you can uncomment next line
        // echo "Mailer Error: " . $mail->ErrorInfo;
        
        return false;
    }
}

/**
 * Send Email with Template
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $templateName Template file name (without extension)
 * @param array $data Data to replace in template
 * @return bool Success status
 */
function sendEmailTemplate($to, $subject, $templateName, $data = []) {
    $templatePath = __DIR__ . "/email_templates/{$templateName}.html";
    
    if (!file_exists($templatePath)) {
        error_log("Email template not found: {$templatePath}");
        return false;
    }
    
    $body = file_get_contents($templatePath);
    
    // Replace placeholders with actual data
    foreach ($data as $key => $value) {
        $body = str_replace("{{{$key}}}", $value, $body);
    }
    
    return sendEmail($to, $subject, $body);
}
?>