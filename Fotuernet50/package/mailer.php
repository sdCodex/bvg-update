<?php
// mailer.php
class SimpleMailer {
    private $smtp_host = 'smtp.gmail.com'; // अपना SMTP host
    private $smtp_port = 465; // या 465 SSL के लिए
    private $smtp_username = 'arunkumarbind150720@gmail.com'; // अपना email
    private $smtp_password = 'xfst ffax xmya zmjl'; // Google App Password
    private $admin_email = 'admin@example.com'; // Admin email
    private $sender_name = 'Fotuernet50 Scholarship';

    public function __construct() {
        // Optional: Constructor में settings set कर सकते हैं
    }

    // ✅ Admin को Registration Backup भेजें
    public function sendBackupToAdmin($student_data, $transaction_id, $amount, $backup_dir) {
        try {
            error_log("📧 STARTING EMAIL TO ADMIN...");
            
            // Email content
            $subject = "📋 New Registration: " . $student_data['unique_id'];
            
            $message = $this->createAdminEmailHTML($student_data, $transaction_id, $amount);
            $text_message = $this->createAdminEmailText($student_data, $transaction_id, $amount);
            
            // Attachments तैयार करें
            $attachments = $this->getAttachments($student_data, $backup_dir);
            
            // Email भेजें
            $result = $this->sendEmail(
                $this->admin_email,
                $subject,
                $message,
                $text_message,
                $attachments,
                true // HTML format
            );
            
            if ($result) {
                error_log("✅ ADMIN EMAIL SENT SUCCESSFULLY");
                return true;
            } else {
                error_log("❌ ADMIN EMAIL FAILED");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("💥 EMAIL ERROR: " . $e->getMessage());
            return false;
        }
    }

    // ✅ Student को Confirmation भेजें
    public function sendConfirmationToStudent($student_data, $transaction_id, $amount) {
        try {
            error_log("📧 SENDING CONFIRMATION TO STUDENT...");
            
            $subject = "✅ Registration Confirmed - " . $student_data['unique_id'];
            
            $message = $this->createStudentEmailHTML($student_data, $transaction_id, $amount);
            $text_message = $this->createStudentEmailText($student_data, $transaction_id, $amount);
            
            $result = $this->sendEmail(
                $student_data['email'],
                $subject,
                $message,
                $text_message,
                [],
                true
            );
            
            if ($result) {
                error_log("✅ STUDENT CONFIRMATION EMAIL SENT");
                return true;
            } else {
                error_log("❌ STUDENT EMAIL FAILED");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("💥 STUDENT EMAIL ERROR: " . $e->getMessage());
            return false;
        }
    }

    // ✅ Main Email Function
    private function sendEmail($to, $subject, $html_message, $text_message, $attachments = [], $is_html = true) {
        try {
            // Mail headers
            $headers = [
                'From' => $this->sender_name . ' <' . $this->smtp_username . '>',
                'Reply-To' => $this->smtp_username,
                'Return-Path' => $this->smtp_username,
                'X-Mailer' => 'PHP/' . phpversion(),
                'MIME-Version' => '1.0'
            ];

            // HTML email के लिए
            if ($is_html) {
                $boundary = md5(time());
                $headers['Content-Type'] = 'multipart/mixed; boundary="' . $boundary . '"';
                
                // Message body
                $body = "--" . $boundary . "\r\n";
                $body .= "Content-Type: text/html; charset=UTF-8\r\n";
                $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                $body .= $html_message . "\r\n\r\n";
                
                // Text alternative
                $body .= "--" . $boundary . "\r\n";
                $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                $body .= $text_message . "\r\n\r\n";
                
                // Attachments
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment['path'])) {
                        $file_content = file_get_contents($attachment['path']);
                        $file_encoded = chunk_split(base64_encode($file_content));
                        
                        $body .= "--" . $boundary . "\r\n";
                        $body .= "Content-Type: " . $attachment['type'] . "; name=\"" . $attachment['name'] . "\"\r\n";
                        $body .= "Content-Transfer-Encoding: base64\r\n";
                        $body .= "Content-Disposition: attachment; filename=\"" . $attachment['name'] . "\"\r\n\r\n";
                        $body .= $file_encoded . "\r\n\r\n";
                    }
                }
                
                $body .= "--" . $boundary . "--";
                
            } else {
                // Plain text
                $headers['Content-Type'] = 'text/plain; charset=UTF-8';
                $body = $text_message;
            }
            
            // Headers को string में convert करें
            $headers_string = '';
            foreach ($headers as $key => $value) {
                $headers_string .= "$key: $value\r\n";
            }
            
            // Send mail
            $result = mail($to, $subject, $body, $headers_string);
            
            if (!$result) {
                error_log("📧 MAIL() FUNCTION FAILED");
                
                // Alternative: PHPMailer या SMTP का use करें
                return $this->sendViaSMTP($to, $subject, $html_message, $attachments);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("💥 SEND EMAIL ERROR: " . $e->getMessage());
            return false;
        }
    }

    // ✅ SMTP के जरिए Email भेजें (Alternative)
    private function sendViaSMTP($to, $subject, $html_message, $attachments = []) {
        try {
            // यदि PHPMailer install है तो उसका use करें
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                return $this->sendWithPHPMailer($to, $subject, $html_message, $attachments);
            }
            
            // या फिर fsockopen से SMTP
            return $this->sendWithSMTPSocket($to, $subject, $html_message);
            
        } catch (Exception $e) {
            error_log("💥 SMTP ERROR: " . $e->getMessage());
            return false;
        }
    }

    // ✅ PHPMailer का use करें (Recommended)
    private function sendWithPHPMailer($to, $subject, $html_message, $attachments) {
        require_once 'PHPMailer/PHPMailer.php';
        require_once 'PHPMailer/SMTP.php';
        require_once 'PHPMailer/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp_username;
            $mail->Password = $this->smtp_password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->smtp_port;
            
            // Recipients
            $mail->setFrom($this->smtp_username, $this->sender_name);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_message;
            $mail->AltBody = strip_tags($html_message);
            
            // Attachments
            foreach ($attachments as $attachment) {
                if (file_exists($attachment['path'])) {
                    $mail->addAttachment($attachment['path'], $attachment['name']);
                }
            }
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("💥 PHPMailer ERROR: " . $mail->ErrorInfo);
            return false;
        }
    }

    // ✅ Attachments list बनाएं
    private function getAttachments($student_data, $backup_dir) {
        $attachments = [];
        $unique_id = $student_data['unique_id'];
        
        // JSON Backup
        $json_file = $backup_dir . $unique_id . '.json';
        if (file_exists($json_file)) {
            $attachments[] = [
                'path' => $json_file,
                'name' => $unique_id . '.json',
                'type' => 'application/json'
            ];
        }
        
        // TXT Backup
        $txt_file = $backup_dir . $unique_id . '.txt';
        if (file_exists($txt_file)) {
            $attachments[] = [
                'path' => $txt_file,
                'name' => $unique_id . '.txt',
                'type' => 'text/plain'
            ];
        }
        
        return $attachments;
    }

    // ✅ Admin Email HTML Template
    private function createAdminEmailHTML($student_data, $transaction_id, $amount) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                .details { margin: 15px 0; }
                .details td { padding: 8px; border-bottom: 1px solid #ddd; }
                .highlight { background: #e8f5e9; padding: 10px; border-left: 4px solid #4CAF50; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📋 New Student Registration</h1>
                    <p>Fotuernet50 Scholarship Program</p>
                </div>
                
                <div class="content">
                    <div class="highlight">
                        <h3>✅ New Registration Received Successfully</h3>
                        <p>Registration ID: <strong>' . $student_data['unique_id'] . '</strong></p>
                        <p>Date: ' . date('d/m/Y H:i:s') . '</p>
                    </div>
                    
                    <h3>Student Details:</h3>
                    <table class="details" width="100%">
                        <tr><td><strong>Name:</strong></td><td>' . $student_data['name'] . '</td></tr>
                        <tr><td><strong>Father Name:</strong></td><td>' . $student_data['father_name'] . '</td></tr>
                        <tr><td><strong>Class:</strong></td><td>' . $student_data['class'] . '</td></tr>
                        <tr><td><strong>School:</strong></td><td>' . $student_data['school_name'] . '</td></tr>
                        <tr><td><strong>Email:</strong></td><td>' . $student_data['email'] . '</td></tr>
                        <tr><td><strong>Phone:</strong></td><td>' . $student_data['phone'] . '</td></tr>
                        <tr><td><strong>City:</strong></td><td>' . $student_data['city'] . '</td></tr>
                    </table>
                    
                    <h3>Payment Details:</h3>
                    <table class="details" width="100%">
                        <tr><td><strong>Transaction ID:</strong></td><td>' . $transaction_id . '</td></tr>
                        <tr><td><strong>Amount:</strong></td><td>₹' . $amount . '</td></tr>
                        <tr><td><strong>Payment Method:</strong></td><td>PhonePe</td></tr>
                        <tr><td><strong>Payment Status:</strong></td><td><span style="color:green;">✅ Success</span></td></tr>
                    </table>
                    
                    <p><em>Backup files are attached to this email.</em></p>
                </div>
                
                <div class="footer">
                    <p>This is an automated email from Fotuernet50 Scholarship System.</p>
                    <p>© ' . date('Y') . ' Fotuernet50. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    // ✅ Admin Email Text Version
    private function createAdminEmailText($student_data, $transaction_id, $amount) {
        return "
NEW STUDENT REGISTRATION
========================

Registration ID: " . $student_data['unique_id'] . "
Date: " . date('d/m/Y H:i:s') . "

STUDENT DETAILS:
---------------
Name: " . $student_data['name'] . "
Father Name: " . $student_data['father_name'] . "
Class: " . $student_data['class'] . "
School: " . $student_data['school_name'] . "
Email: " . $student_data['email'] . "
Phone: " . $student_data['phone'] . "
City: " . $student_data['city'] . "

PAYMENT DETAILS:
---------------
Transaction ID: " . $transaction_id . "
Amount: ₹" . $amount . "
Payment Method: PhonePe
Payment Status: Success

Backup files are attached to this email.

This is an automated email from Fotuernet50 Scholarship System.
";
    }

    // ✅ Student Email HTML Template
    private function createStudentEmailHTML($student_data, $transaction_id, $amount) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2196F3; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                .highlight { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Registration Confirmed!</h1>
                    <p>Fotuernet50 Scholarship Program</p>
                </div>
                
                <div class="content">
                    <div class="highlight">
                        <h2>Dear ' . $student_data['name'] . ',</h2>
                        <p>Your registration has been successfully completed and payment is confirmed.</p>
                    </div>
                    
                    <h3>Your Registration Details:</h3>
                    <p><strong>Registration ID:</strong> ' . $student_data['unique_id'] . '</p>
                    <p><strong>Transaction ID:</strong> ' . $transaction_id . '</p>
                    <p><strong>Amount Paid:</strong> ₹' . $amount . '</p>
                    <p><strong>Payment Status:</strong> <span style="color:green;">✅ Success</span></p>
                    
                    <p>Please keep your Registration ID for future reference.</p>
                    
                    <p>For any queries, contact us at:<br>
                    Email: support@fotuernet50.com<br>
                    Phone: +91-XXXXXXXXXX</p>
                </div>
                
                <div class="footer">
                    <p>Thank you for registering with Fotuernet50 Scholarship Program.</p>
                    <p>© ' . date('Y') . ' Fotuernet50. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    // ✅ Student Email Text Version
    private function createStudentEmailText($student_data, $transaction_id, $amount) {
        return "
REGISTRATION CONFIRMATION
=========================

Dear " . $student_data['name'] . ",

Your registration has been successfully completed and payment is confirmed.

Your Registration Details:
-------------------------
Registration ID: " . $student_data['unique_id'] . "
Transaction ID: " . $transaction_id . "
Amount Paid: ₹" . $amount . "
Payment Status: Success

Please keep your Registration ID for future reference.

For any queries, contact us at:
Email: support@fotuernet50.com
Phone: +91-XXXXXXXXXX

Thank you for registering with Fotuernet50 Scholarship Program.
";
    }

    // ✅ Custom Email भेजने के लिए
    public function sendCustomEmail($to, $subject, $message, $is_html = true) {
        $text_message = strip_tags($message);
        return $this->sendEmail($to, $subject, $message, $text_message, [], $is_html);
    }
}
?>