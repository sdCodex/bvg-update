<?php
// includes/mailer.php
class SimpleMailer {
    private $admin_email = "admin@example.com"; // अपना email डालें
    private $from_email = "noreply@yourdomain.com";
    private $from_name = "Fortunate 51 Scholarship";
    
    public function __construct($admin_email = null) {
        if ($admin_email) {
            $this->admin_email = $admin_email;
        }
    }
    
    /**
     * Send TXT backup file and data to admin via email
     */
    public function sendBackupToAdmin($student_data, $transaction_id, $amount, $backup_dir) {
        try {
            $unique_id = $student_data['unique_id'];
            $txt_file = $backup_dir . $unique_id . '.txt';
            
            // Check if TXT file exists
            if (!file_exists($txt_file)) {
                error_log("❌ TXT FILE NOT FOUND FOR EMAIL: " . $txt_file);
                return false;
            }
            
            // Email details
            $to = $this->admin_email;
            $subject = "📋 New Registration - " . $student_data['name'] . " [" . $unique_id . "]";
            
            // Email body (HTML + Plain text)
            $html_body = $this->prepareEmailBody($student_data, $transaction_id, $amount, $txt_file);
            $plain_body = $this->preparePlainTextBody($student_data, $transaction_id, $amount);
            
            // Headers
            $headers = $this->prepareHeaders();
            
            // Boundary for attachment
            $boundary = md5(time());
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";
            
            // Message with attachment
            $message = "--" . $boundary . "\r\n";
            $message .= "Content-Type: multipart/alternative; boundary=\"alt-" . $boundary . "\"\r\n\r\n";
            
            // Plain text version
            $message .= "--alt-" . $boundary . "\r\n";
            $message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $plain_body . "\r\n\r\n";
            
            // HTML version
            $message .= "--alt-" . $boundary . "\r\n";
            $message .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $html_body . "\r\n\r\n";
            $message .= "--alt-" . $boundary . "--\r\n\r\n";
            
            // Attachment
            $file_content = file_get_contents($txt_file);
            $file_encoded = chunk_split(base64_encode($file_content));
            
            $message .= "--" . $boundary . "\r\n";
            $message .= "Content-Type: application/octet-stream; name=\"" . basename($txt_file) . "\"\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n";
            $message .= "Content-Disposition: attachment; filename=\"" . basename($txt_file) . "\"\r\n\r\n";
            $message .= $file_encoded . "\r\n\r\n";
            $message .= "--" . $boundary . "--";
            
            // Send email
            if (mail($to, $subject, $message, $headers)) {
                error_log("📧 EMAIL SENT SUCCESSFULLY TO ADMIN: " . $to);
                
                // Also send to student email (optional)
                $this->sendConfirmationToStudent($student_data, $transaction_id, $amount, $txt_file);
                
                return true;
            } else {
                error_log("❌ EMAIL SENDING FAILED");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("💥 EMAIL ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send confirmation to student
     */
    private function sendConfirmationToStudent($student_data, $transaction_id, $amount, $txt_file) {
        if (empty($student_data['email'])) {
            return false;
        }
        
        try {
            $to = $student_data['email'];
            $subject = "✅ Registration Successful - Fortunate 51 Scholarship";
            
            $html_body = $this->prepareStudentEmailBody($student_data, $transaction_id, $amount);
            $plain_body = $this->prepareStudentPlainTextBody($student_data, $transaction_id, $amount);
            
            $headers = $this->prepareHeaders();
            $boundary = md5(time() . 'student');
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";
            
            $message = "--" . $boundary . "\r\n";
            $message .= "Content-Type: multipart/alternative; boundary=\"alt-" . $boundary . "\"\r\n\r\n";
            
            // Plain text
            $message .= "--alt-" . $boundary . "\r\n";
            $message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $plain_body . "\r\n\r\n";
            
            // HTML
            $message .= "--alt-" . $boundary . "\r\n";
            $message .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $html_body . "\r\n\r\n";
            $message .= "--alt-" . $boundary . "--\r\n\r\n";
            
            // Attachment (TXT file)
            $file_content = file_get_contents($txt_file);
            $file_encoded = chunk_split(base64_encode($file_content));
            
            $message .= "--" . $boundary . "\r\n";
            $message .= "Content-Type: application/octet-stream; name=\"" . basename($txt_file) . "\"\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n";
            $message .= "Content-Disposition: attachment; filename=\"Registration_Receipt.txt\"\r\n\r\n";
            $message .= $file_encoded . "\r\n\r\n";
            $message .= "--" . $boundary . "--";
            
            if (mail($to, $subject, $message, $headers)) {
                error_log("📧 CONFIRMATION EMAIL SENT TO STUDENT: " . $to);
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("💥 STUDENT EMAIL ERROR: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Prepare headers for email
     */
    private function prepareHeaders() {
        $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        $headers .= "Reply-To: " . $this->from_email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        return $headers;
    }
    
    /**
     * Prepare HTML email body for admin
     */
    private function prepareEmailBody($student_data, $transaction_id, $amount, $txt_file) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 700px; margin: 0 auto; padding: 20px; }
                .header { background: #4CAF50; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; }
                .details { margin: 15px 0; }
                .label { font-weight: bold; color: #333; }
                .footer { margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>🎉 New Scholarship Registration</h2>
                </div>
                
                <div class="content">
                    <h3>Student Details:</h3>
                    <div class="details">
                        <p><span class="label">Registration ID:</span> ' . $student_data['unique_id'] . '</p>
                        <p><span class="label">Name:</span> ' . $student_data['name'] . '</p>
                        <p><span class="label">Father\'s Name:</span> ' . $student_data['father_name'] . '</p>
                        <p><span class="label">Phone:</span> ' . $student_data['phone'] . '</p>
                        <p><span class="label">Email:</span> ' . $student_data['email'] . '</p>
                        <p><span class="label">Class:</span> ' . $student_data['class'] . '</p>
                        <p><span class="label">School:</span> ' . $student_data['school_name'] . '</p>
                    </div>
                    
                    <h3>Payment Details:</h3>
                    <div class="details">
                        <p><span class="label">Transaction ID:</span> ' . $transaction_id . '</p>
                        <p><span class="label">Amount:</span> ₹' . $amount . '</p>
                        <p><span class="label">Payment Status:</span> ✅ Success</p>
                    </div>
                    
                    <h3>📎 Attachment:</h3>
                    <p>A detailed TXT backup file is attached to this email.</p>
                    
                    <div class="footer">
                        <p><strong>📊 Complete Registration Data:</strong></p>
                        <p>Registration Time: ' . date('d/m/Y h:i A') . '</p>
                        <p>This is an automated email from Fortunate 51 Scholarship System.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Prepare plain text email body for admin
     */
    private function preparePlainTextBody($student_data, $transaction_id, $amount) {
        $text = "NEW REGISTRATION - FORTUINATE 51 SCHOLARSHIP\n";
        $text .= "=============================================\n\n";
        
        $text .= "STUDENT DETAILS:\n";
        $text .= "----------------\n";
        $text .= "Registration ID: " . $student_data['unique_id'] . "\n";
        $text .= "Name: " . $student_data['name'] . "\n";
        $text .= "Father's Name: " . $student_data['father_name'] . "\n";
        $text .= "Phone: " . $student_data['phone'] . "\n";
        $text .= "Email: " . $student_data['email'] . "\n";
        $text .= "Class: " . $student_data['class'] . "\n";
        $text .= "School: " . $student_data['school_name'] . "\n\n";
        
        $text .= "PAYMENT DETAILS:\n";
        $text .= "----------------\n";
        $text .= "Transaction ID: " . $transaction_id . "\n";
        $text .= "Amount: ₹" . $amount . "\n";
        $text .= "Status: Success\n\n";
        
        $text .= "A detailed backup file is attached to this email.\n\n";
        
        $text .= "Generated on: " . date('d/m/Y h:i A') . "\n";
        $text .= "=============================================\n";
        
        return $text;
    }
    
    /**
     * Prepare student email body
     */
    private function prepareStudentEmailBody($student_data, $transaction_id, $amount) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 700px; margin: 0 auto; padding: 20px; }
                .header { background: #2196F3; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; }
                .details { margin: 15px 0; }
                .label { font-weight: bold; color: #333; }
                .receipt { background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>✅ Registration Successful!</h2>
                    <h3>Fortunate 51 Scholarship</h3>
                </div>
                
                <div class="content">
                    <p>Dear ' . $student_data['name'] . ',</p>
                    
                    <p>Thank you for registering for the Fortunate 51 Scholarship program. 
                    Your registration has been successfully completed.</p>
                    
                    <div class="receipt">
                        <h3>📋 Registration Receipt:</h3>
                        <p><span class="label">Registration ID:</span> ' . $student_data['unique_id'] . '</p>
                        <p><span class="label">Transaction ID:</span> ' . $transaction_id . '</p>
                        <p><span class="label">Amount Paid:</span> ₹' . $amount . '</p>
                        <p><span class="label">Date:</span> ' . date('d/m/Y') . '</p>
                    </div>
                    
                    <h3>📎 What attached?</h3>
                    <p>We have attached a detailed receipt in TXT format with all your registration details.</p>
                    
                    <div class="footer">
                        <p>For any queries, please contact us.</p>
                        <p><strong>Best Regards,</strong><br>
                        Fortunate 51 Scholarship Team</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Prepare student plain text email
     */
    private function prepareStudentPlainTextBody($student_data, $transaction_id, $amount) {
        $text = "REGISTRATION CONFIRMATION - FORTUINATE 51 SCHOLARSHIP\n";
        $text .= "=====================================================\n\n";
        
        $text .= "Dear " . $student_data['name'] . ",\n\n";
        
        $text .= "Thank you for registering for the Fortunate 51 Scholarship program.\n";
        $text .= "Your registration has been successfully completed.\n\n";
        
        $text .= "REGISTRATION DETAILS:\n";
        $text .= "---------------------\n";
        $text .= "Registration ID: " . $student_data['unique_id'] . "\n";
        $text .= "Transaction ID: " . $transaction_id . "\n";
        $text .= "Amount Paid: ₹" . $amount . "\n";
        $text .= "Date: " . date('d/m/Y') . "\n\n";
        
        $text .= "A detailed receipt is attached to this email.\n\n";
        
        $text .= "For any queries, please contact us.\n\n";
        
        $text .= "Best Regards,\n";
        $text .= "Fortunate 51 Scholarship Team\n";
        $text .= "=====================================================\n";
        
        return $text;
    }
}