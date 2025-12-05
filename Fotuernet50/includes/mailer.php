<?php
// includes/mailer.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SimpleMailer {
    
    private $mail;
    
    public function __construct() {
        // ✅ PHPMailer ऑटोलोड
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
    }
    
    private function setupSMTP() {
        try {
            // ✅ Gmail SMTP Configuration
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = 'your-email@gmail.com'; // अपना Gmail
            $this->mail->Password   = 'your-app-password';    // App Password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            
            // ✅ Sender Info
            $this->mail->setFrom('noreply@fortunate51.edu.in', 'Fortunate 51 Scholarship');
            $this->mail->isHTML(true);
            
        } catch (Exception $e) {
            error_log("❌ MAILER ERROR: " . $e->getMessage());
        }
    }
    
    public function sendBackupToAdmin($student_data, $transaction_id, $amount, $backup_dir) {
        try {
            $txt_file = $backup_dir . $student_data['unique_id'] . '.txt';
            
            if (!file_exists($txt_file)) {
                throw new Exception("TXT file not found");
            }
            
            // ✅ Recipients
            $this->mail->clearAddresses();
            $this->mail->addAddress('admin@fortunate51.edu.in', 'Admin');
            // $this->mail->addCC('manager@fortunate51.edu.in', 'Manager'); // Optional
            
            // ✅ Subject
            $this->mail->Subject = '🎓 New Registration: ' . $student_data['name'];
            
            // ✅ HTML Body
            $this->mail->Body = $this->createEmailBody($student_data, $transaction_id, $amount);
            
            // ✅ Plain Text Body
            $this->mail->AltBody = $this->createPlainTextBody($student_data, $transaction_id, $amount);
            
            // ✅ Attachment
            $this->mail->addAttachment($txt_file, $student_data['unique_id'] . '.txt');
            
            // ✅ Send
            $this->mail->send();
            
            error_log("✅ EMAIL SENT TO ADMIN: " . $student_data['unique_id']);
            return true;
            
        } catch (Exception $e) {
            error_log("❌ EMAIL FAILED: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    private function createEmailBody($student_data, $transaction_id, $amount) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background: #800000; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                td { padding: 8px; border: 1px solid #ddd; }
                .label { background: #e9ecef; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>FORTUNATE 51 SCHOLARSHIP</h2>
                    <h3>New Registration Notification</h3>
                </div>
                
                <div class="content">
                    <h3>📋 Registration Summary</h3>
                    
                    <table>
                        <tr><td class="label">Registration ID</td><td>' . $student_data['unique_id'] . '</td></tr>
                        <tr><td class="label">Student Name</td><td>' . $student_data['name'] . '</td></tr>
                        <tr><td class="label">Father\'s Name</td><td>' . $student_data['father_name'] . '</td></tr>
                        <tr><td class="label">Class</td><td>Class ' . $student_data['class'] . '</td></tr>
                        <tr><td class="label">School</td><td>' . $student_data['school_name'] . '</td></tr>
                        <tr><td class="label">City</td><td>' . $student_data['city'] . '</td></tr>
                        <tr><td class="label">Phone</td><td>' . $student_data['phone'] . '</td></tr>
                        <tr><td class="label">Transaction ID</td><td>' . $transaction_id . '</td></tr>
                        <tr><td class="label">Amount</td><td>₹' . $amount . '</td></tr>
                        <tr><td class="label">Time</td><td>' . date('d/m/Y h:i A') . '</td></tr>
                    </table>
                    
                    <p><strong>📎 Complete details are attached as text file.</strong></p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
    
    private function createPlainTextBody($student_data, $transaction_id, $amount) {
        return "
        FORTUNATE 51 SCHOLARSHIP - New Registration
        
        Registration ID: {$student_data['unique_id']}
        Student Name: {$student_data['name']}
        Father's Name: {$student_data['father_name']}
        Class: Class {$student_data['class']}
        School: {$student_data['school_name']}
        City: {$student_data['city']}
        Phone: {$student_data['phone']}
        
        Payment Details:
        Transaction ID: {$transaction_id}
        Amount: ₹{$amount}
        Time: " . date('d/m/Y h:i A') . "
        
        Complete details are attached as text file.
        ";
    }
}
?>