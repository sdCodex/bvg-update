<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/PhonePeHelper.php';

// DATABASE CONNECTION
$db_attempts = [
    __DIR__ . '/../includes/db.php',
    __DIR__ . '/../../includes/db.php',
    $_SERVER['DOCUMENT_ROOT'] . '/Fotuernet50/includes/db.php'
];

$db_connected = false;
foreach ($db_attempts as $db_path) {
    if (file_exists($db_path)) {
        require_once $db_path;
        if (isset($pdo)) {
            $db_connected = true;
            break;
        }
    }
}

if (!$db_connected) {
    die("Database connection failed. Please check the includes path.");
}

// ✅ PHP MAILER FUNCTIONS START
function processSingleEmailAndCSV($student_data, $transaction_id, $amount, $payment_method) {
    $backup_dir = __DIR__ . '/../backups/';
    
    // ✅ PEHLE CHECK KARO KI PEHLE SE PROCESS TO NAHI HUA
    $tracker_file = $backup_dir . 'processed_registrations.txt';
    
    // Backup folder create karo
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }
    
    // ✅ FILE TRACKING CHECK
    if (file_exists($tracker_file)) {
        $processed_ids = file($tracker_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (in_array($student_data['unique_id'], $processed_ids)) {
            error_log("📧 EMAIL & CSV ALREADY PROCESSED - SKIPPING: " . $student_data['unique_id']);
            return true;
        }
    }
    
    // ✅ SESSION TRACKING CHECK
    if (isset($_SESSION['processed_' . $student_data['unique_id']]) && 
        $_SESSION['processed_' . $student_data['unique_id']] === true) {
        error_log("📧 ALREADY PROCESSED (SESSION) - SKIPPING: " . $student_data['unique_id']);
        return true;
    }

    $success = true;
    
    // ✅ 1. PEHLE CSV ENTRY KARO
    if (!saveSingleCSVEntry($student_data, $transaction_id, $amount, $payment_method, $backup_dir)) {
        $success = false;
        error_log("❌ CSV ENTRY FAILED");
    }
    
    // ✅ 2. PHIR EMAIL SEND KARO
    if (!sendSingleConfirmationEmail($student_data, $transaction_id, $amount, $payment_method)) {
        $success = false;
        error_log("❌ EMAIL SENDING FAILED");
    }
    
    // ✅ 3. AGAR DONO SUCCESS TO TRACK MARK KARO
    if ($success) {
        // FILE TRACKING
        file_put_contents($tracker_file, $student_data['unique_id'] . PHP_EOL, FILE_APPEND | LOCK_EX);
        
        // SESSION TRACKING
        $_SESSION['processed_' . $student_data['unique_id']] = true;
        
        error_log("🎊 EMAIL & CSV BOTH PROCESSED SUCCESSFULLY: " . $student_data['unique_id']);
    }
    
    return $success;
}

function saveSingleCSVEntry($student_data, $transaction_id, $amount, $payment_method, $backup_dir) {
    $csv_file = $backup_dir . 'all_registrations.csv';
    
    // ✅ PEHLE CHECK KARO KI ENTRY PEHLE SE TO NAHI HAI
    if (file_exists($csv_file)) {
        $file_content = file_get_contents($csv_file);
        if (strpos($file_content, $student_data['unique_id']) !== false) {
            error_log("📄 CSV ENTRY ALREADY EXISTS - SKIPPING: " . $student_data['unique_id']);
            return true;
        }
    }
    
    $csv_headers = [
        'Registration ID', 
        'Transaction ID', 
        'Name', 
        'Father Name', 
        'Mother Name',
        'Email', 
        'Phone', 
        'Class', 
        'School Name',
        'City',
        'State',
        'Amount', 
        'Payment Method',
        'Payment Date',
        'Email Sent'
    ];

    // Agar file nahi hai to headers add karo
    if (!file_exists($csv_file)) {
        $fp = fopen($csv_file, 'w');
        if ($fp) {
            fputcsv($fp, $csv_headers);
            fclose($fp);
            error_log("📄 CSV FILE CREATED WITH HEADERS");
        } else {
            error_log("❌ CSV FILE CREATION FAILED");
            return false;
        }
    }

    // Data add karo
    $fp = fopen($csv_file, 'a');
    if ($fp) {
        $csv_data = [
            $student_data['unique_id'],
            $transaction_id,
            $student_data['name'],
            $student_data['father_name'],
            $student_data['mother_name'] ?? 'N/A',
            $student_data['email'],
            $student_data['phone'],
            $student_data['class'],
            $student_data['school_name'],
            $student_data['city'],
            $student_data['state'],
            $amount,
            $payment_method,
            date('Y-m-d H:i:s'),
            'Yes'
        ];
        
        fputcsv($fp, $csv_data);
        fclose($fp);
        error_log("📄 SINGLE CSV ENTRY ADDED: " . $student_data['unique_id']);
        return true;
    } else {
        error_log("❌ CSV FILE OPEN FAILED");
        return false;
    }
}

function sendSingleConfirmationEmail($student_data, $transaction_id, $amount, $payment_method) {
    error_log("🚀 STARTING EMAIL SEND FUNCTION...");
    
    // ✅ PHPMailer PATHS - APNE STRUCTURE KE HISAB SE
    $phpmailer_paths = [
        __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php',
        __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php',
        __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php',
        __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php',
        __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php',
        __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php'
    ];

    $phpmailer_loaded = false;
    foreach ($phpmailer_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $phpmailer_loaded = true;
            error_log("✅ PHPMailer loaded from: " . $path);
            break;
        }
    }

    if (!$phpmailer_loaded) {
        error_log("❌ PHPMailer files not found in any path");
        
        // ✅ ALTERNATIVE: Autoloader try karo
        $autoload_path = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoload_path)) {
            require_once $autoload_path;
            $phpmailer_loaded = true;
            error_log("✅ Autoloader loaded PHPMailer");
        } else {
            error_log("❌ Autoloader also not found");
            return false;
        }
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // ✅ SMTP Configuration - SIMPLIFIED
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cmd@ourgurukul.org';
        $mail->Password   = 'swdrepfqffddfjuk';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->Timeout    = 30;
        $mail->CharSet    = 'UTF-8';
        
        // ✅ DEBUGGING - DISABLE FOR PRODUCTION
        $mail->SMTPDebug = 0; // Disable debug output for production
        // $mail->Debugoutput = function($str, $level) {
        //     error_log("📧 SMTP DEBUG: $str");
        // };

        // ✅ CORRECT Email recipients
        $mail->setFrom('cmd@ourgurukul.org', 'Fortunate 51 Scholarship');
        $mail->addAddress('info@ourgurukul.org', 'Fortunate 51 Admin');
        
        // ✅ STUDENT KO BHI BCC KARO
        if (!empty($student_data['email'])) {
            $mail->addBCC($student_data['email'], $student_data['name']);
            error_log("📧 STUDENT BCC ADDED: " . $student_data['email']);
        }
        
        $mail->isHTML(true);
        $mail->Subject = 'Fortunate 51 Scholarship Application - ' . $student_data['unique_id'];
        
        $mail->Body = createEmailTemplate($student_data, $transaction_id, $amount, $payment_method);
        $mail->AltBody = createPlainTextEmail($student_data, $transaction_id, $amount, $payment_method);

        // Send email
        error_log("📧 ATTEMPTING TO SEND EMAIL...");
        if ($mail->send()) {
            error_log("✅ CONFIRMATION EMAIL SENT SUCCESSFULLY");
            return true;
        } else {
            error_log("❌ EMAIL SEND FAILED: " . $mail->ErrorInfo);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("💥 EMAIL EXCEPTION: " . $e->getMessage());
        return false;
    }
}

function createEmailTemplate($student_data, $transaction_id, $amount, $payment_method) {
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
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>BHAKTIVEDANTA GURUKUL</h1>
                <h2>FORTUNATE 51 - Scholarship Registration</h2>
            </div>
            
            <div class='content'>
                <h3>✅ PAYMENT SUCCESSFUL</h3>
                <p>Dear Admin,</p>
                <p>A new scholarship application has been successfully registered and payment has been received.</p>
                
                <div class='details'>
                    <h3>Application Details:</h3>
                    <table>
                        <tr>
                            <td><strong>Application No:</strong></td>
                            <td>{$student_data['unique_id']}</td>
                        </tr>
                        <tr>
                            <td><strong>Student Name:</strong></td>
                            <td>{$student_data['name']}</td>
                        </tr>
                        <tr>
                            <td><strong>Father's Name:</strong></td>
                            <td>{$student_data['father_name']}</td>
                        </tr>
                        <tr>
                            <td><strong>Class:</strong></td>
                            <td>{$student_data['class']}</td>
                        </tr>
                        <tr>
                            <td><strong>School:</strong></td>
                            <td>{$student_data['school_name']}</td>
                        </tr>
                        <tr>
                            <td><strong>Transaction ID:</strong></td>
                            <td>{$transaction_id}</td>
                        </tr>
                        <tr>
                            <td><strong>Amount:</strong></td>
                            <td>₹{$amount}</td>
                        </tr>
                        <tr>
                            <td><strong>Payment Method:</strong></td>
                            <td>{$payment_method}</td>
                        </tr>
                        <tr>
                            <td><strong>Payment Date:</strong></td>
                            <td>{$payment_date}</td>
                        </tr>
                    </table>
                </div>
                
                <p><em>This is an automated email. Please do not reply.</em></p>
            </div>
        </div>
    </body>
    </html>
    ";
}

function createPlainTextEmail($student_data, $transaction_id, $amount, $payment_method) {
    $payment_date = date('d/m/Y h:i A');
    
    return "
Fortunate 51 SCHOLARSHIP - REGISTRATION CONFIRMATION
==============================================

PAYMENT SUCCESSFUL
Application No: {$student_data['unique_id']}
Student Name: {$student_data['name']}
Father's Name: {$student_data['father_name']}
Class: {$student_data['class']}
School: {$student_data['school_name']}
Transaction ID: {$transaction_id}
Amount: ₹{$amount}
Payment Method: {$payment_method}
Payment Date: {$payment_date}

This is an automated email. Please do not reply.
    ";
}
// ✅ PHP MAILER FUNCTIONS END

// ✅ DEBUG: Check what order_id we received
$order_id = $_GET['order_id'] ?? '';
error_log("🎯 STARTING PAYMENT VERIFICATION FOR: " . $order_id);

// ✅ PEHLE HI BACKUP FOLDER CREATE KARO
$backup_dir = __DIR__ . '/../backups/';
if (!is_dir($backup_dir)) {
    if (mkdir($backup_dir, 0777, true)) {
        error_log("📁 BACKUP FOLDER CREATED: " . $backup_dir);
    } else {
        error_log("❌ BACKUP FOLDER CREATION FAILED: " . $backup_dir);
    }
}

try {
    // VALIDATE order_id   
    if (empty($order_id)) {
        die("No Transaction Id Found in URL");
    }

    // ✅ Get complete student data BEFORE update
    $check_stmt = $pdo->prepare("SELECT * FROM fotuernet50_students WHERE unique_id = ?");
    $check_stmt->execute([$order_id]);
    $student_data = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student_data) {
        die("Student record not found for ID: " . $order_id);
    }

    error_log("📊 CURRENT DB STATUS:");
    error_log("   - Payment Status: " . ($student_data['payment_status'] ?? 'NULL'));
    error_log("   - Name: " . ($student_data['name'] ?? 'NULL'));

    $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, ENV);
    $response = $phonePeHelper->checkPaymentStatus($order_id);

    // ✅ DEBUG: Log Complete PhonePe response
    error_log("📱 COMPLETE PHONEPE RESPONSE:");
    error_log(print_r($response, true));

    $payment_success = false;
    $transaction_id = '';
    $amount = 0;
    $payment_method = 'PHONEPE';

    // ✅ IMPROVED PAYMENT STATUS CHECK
    if (($response['state'] ?? '') === 'COMPLETED') {
        error_log("✅ PAYMENT COMPLETED - UPDATING DATABASE");

        // ✅ EXTRACT PAYMENT DETAILS CORRECTLY
        $transaction_id = $response['transactionId'] ?? $response['paymentDetails'][0]['transactionId'] ?? $response['orderId'] ?? $order_id;
        $amount = ($response['amount'] ?? 0) / 100; // Convert to rupees
        $payment_method = $response['paymentMethod'] ?? $response['paymentDetails'][0]['paymentMode'] ?? 'PHONEPE';
        $merchant_id = $response['merchantId'] ?? '';
        $transaction_code = $response['transactionCode'] ?? $transaction_id;

        error_log("💾 UPDATE DATA:");
        error_log("   - Transaction ID: " . $transaction_id);
        error_log("   - Amount: " . $amount);
        error_log("   - Payment Method: " . $payment_method);

        // ✅ PEHLE BACKUP SAVE KARO - DATABASE UPDATE SE PEHLE
        saveBackupImmediately($student_data, $response, $transaction_id, $amount, $payment_method);

        // ✅ COMPLETE DATABASE UPDATE WITH ALL FIELDS
        try {
            $update_sql = "UPDATE fotuernet50_students SET 
                          payment_status = 'success',
                          phonepe_transaction_id = ?,
                          phonepe_merchant_id = ?,
                          amount = ?,
                          payment_method = ?,
                          phonepe_transaction_code = ?,
                          updated_at = NOW()
                          WHERE unique_id = ?";

            $update_stmt = $pdo->prepare($update_sql);
            $update_result = $update_stmt->execute([
                $transaction_id,
                $merchant_id,
                $amount,
                $payment_method,
                $transaction_code,
                $order_id
            ]);

            $affected_rows = $update_stmt->rowCount();

            if ($update_result && $affected_rows > 0) {
                error_log("🎉 DATABASE UPDATED SUCCESSFULLY!");
                $payment_success = true;

                // ✅ UPDATE STUDENT DATA WITH NEW PAYMENT INFO
                $student_data['payment_status'] = 'success';
                $student_data['phonepe_transaction_id'] = $transaction_id;
                $student_data['amount'] = $amount;
                $student_data['payment_method'] = $payment_method;

                // ✅ ✅ ✅ YAHAN SINGLE EMAIL & CSV PROCESSING ✅ ✅ ✅
                if ($payment_success) {
                    error_log("🚀 ATTEMPTING SINGLE EMAIL & CSV PROCESSING...");
                    
                    $processing_result = processSingleEmailAndCSV(
                        $student_data, 
                        $transaction_id, 
                        $amount, 
                        $payment_method
                    );
                    
                    if ($processing_result) {
                        error_log("🎊 EMAIL & CSV BOTH PROCESSED SUCCESSFULLY!");
                        $_SESSION['processing_success'] = "Registration completed successfully! Confirmation email sent.";
                    } else {
                        error_log("⚠️ EMAIL OR CSV PROCESSING FAILED");
                        $_SESSION['processing_warning'] = "Registration successful but email notification failed. Please contact support.";
                    }
                }
                // ✅ ✅ ✅ PROCESSING COMPLETE ✅ ✅ ✅
                
            } else {
                error_log("❌ UPDATE FAILED - No rows affected");

                // ✅ FALLBACK: Only update payment status
                try {
                    $fallback_sql = "UPDATE fotuernet50_students SET 
                                    payment_status = 'success',
                                    updated_at = NOW()
                                    WHERE unique_id = ?";

                    $fallback_stmt = $pdo->prepare($fallback_sql);
                    $fallback_result = $fallback_stmt->execute([$order_id]);

                    if ($fallback_result && $fallback_stmt->rowCount() > 0) {
                        error_log("🔄 FALLBACK UPDATE SUCCESS");
                        $payment_success = true;
                        $student_data['payment_status'] = 'success';
                    }
                } catch (Exception $e) {
                    error_log("💥 FALLBACK UPDATE ERROR: " . $e->getMessage());
                }
            }
        } catch (PDOException $e) {
            error_log("💥 DATABASE ERROR: " . $e->getMessage());
        }
    } else {
        error_log("❌ PAYMENT NOT COMPLETED - State: " . ($response['state'] ?? 'UNKNOWN'));
    }

    // ✅ FINAL VERIFICATION - Get updated data
    $final_stmt = $pdo->prepare("SELECT * FROM fotuernet50_students WHERE unique_id = ?");
    $final_stmt->execute([$order_id]);
    $student_data = $final_stmt->fetch(PDO::FETCH_ASSOC);

    error_log("🔍 FINAL DATABASE STATUS:");
    error_log("   - Payment Status: " . ($student_data['payment_status'] ?? 'NULL'));
    error_log("   - Transaction ID: " . ($student_data['phonepe_transaction_id'] ?? 'NULL'));

    if ($student_data) {
        $_SESSION['student_data'] = $student_data;
    }
} catch (Exception $e) {
    error_log("💥 MAIN ERROR: " . $e->getMessage());
    die("Error: " . $e->getMessage());
}

// ✅ IMMEDIATE BACKUP FUNCTION (Database update se PEHLE)
function saveBackupImmediately($student_data, $response, $transaction_id, $amount, $payment_method)
{
    error_log("💾 STARTING IMMEDIATE BACKUP...");

    $backup_data = [
        'registration_id' => $student_data['unique_id'],
        'transaction_id' => $transaction_id,
        'name' => $student_data['name'],
        'father_name' => $student_data['father_name'],
        'mother_name' => $student_data['mother_name'],
        'gender' => $student_data['gender'],
        'dob' => $student_data['dob'],
        'phone' => $student_data['phone'],
        'alt_contact' => $student_data['alt_contact'],
        'email' => $student_data['email'],
        'aadhaar' => $student_data['aadhaar'],
        'class' => $student_data['class'],
        'school_name' => $student_data['school_name'],
        'city' => $student_data['city'],
        'district' => $student_data['district'],
        'state' => $student_data['state'],
        'pincode' => $student_data['pincode'],
        'address' => $student_data['address'],
        'landmark' => $student_data['landmark'],
        'amount' => $amount,
        'payment_method' => $payment_method,
        'payment_status' => 'success',
        'payment_response' => $response,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Backup folder create karo
    $backup_dir = __DIR__ . '/../backups/';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }

    // JSON file mein save karo
    $filename = $backup_dir . $student_data['unique_id'] . '.json';
    if (file_put_contents($filename, json_encode($backup_data, JSON_PRETTY_PRINT))) {
        error_log("✅ IMMEDIATE BACKUP SAVED: " . $filename);

        // ✅ TXT FILE BHI BANAO - READABLE FORMAT MEIN
        saveToReadableTxt($backup_data, $backup_dir, $student_data['unique_id'], $transaction_id);
    } else {
        error_log("❌ IMMEDIATE BACKUP FAILED");
    }
}

// ✅ READABLE TXT BACKUP FUNCTION WITH TRANSACTION ID
function saveToReadableTxt($data, $backup_dir, $unique_id, $transaction_id)
{
    $txt_file = $backup_dir . $unique_id . '.txt';

    $content = "=============================================\n";
    $content .= "Fortunate 51 SCHOLARSHIP - REGISTRATION CONFIRMATION\n";
    $content .= "=============================================\n\n";

    $content .= "REGISTRATION DETAILS:\n";
    $content .= "=====================\n";
    $content .= "Registration ID: " . $data['registration_id'] . "\n";
    $content .= "Transaction ID: " . $transaction_id . "\n";
    $content .= "Name: " . $data['name'] . "\n";
    $content .= "Father's Name: " . $data['father_name'] . "\n";
    $content .= "Mother's Name: " . $data['mother_name'] . "\n";
    $content .= "Date of Birth: " . $data['dob'] . "\n";
    $content .= "Gender: " . $data['gender'] . "\n";
    $content .= "Aadhaar: " . $data['aadhaar'] . "\n\n";

    $content .= "CONTACT INFORMATION:\n";
    $content .= "====================\n";
    $content .= "Phone: " . $data['phone'] . "\n";
    $content .= "Alternate Phone: " . $data['alt_contact'] . "\n";
    $content .= "Email: " . $data['email'] . "\n\n";

    $content .= "ACADEMIC DETAILS:\n";
    $content .= "=================\n";
    $content .= "Class: " . $data['class'] . "\n";
    $content .= "School: " . $data['school_name'] . "\n\n";

    $content .= "ADDRESS:\n";
    $content .= "========\n";
    $content .= "Address: " . $data['address'] . "\n";
    $content .= "City: " . $data['city'] . "\n";
    $content .= "District: " . $data['district'] . "\n";
    $content .= "State: " . $data['state'] . "\n";
    $content .= "Pincode: " . $data['pincode'] . "\n";
    $content .= "Landmark: " . $data['landmark'] . "\n\n";

    $content .= "PAYMENT DETAILS:\n";
    $content .= "================\n";
    $content .= "Transaction ID: " . $transaction_id . "\n";
    $content .= "Amount: ₹" . $data['amount'] . "\n";
    $content .= "Payment Method: " . $data['payment_method'] . "\n";
    $content .= "Payment Status: " . $data['payment_status'] . "\n";
    $content .= "Payment Date: " . $data['timestamp'] . "\n\n";

    $content .= "=============================================\n";
    $content .= "Generated on: " . date('d/m/Y h:i A') . "\n";
    $content .= "=============================================\n";

    if (file_put_contents($txt_file, $content)) {
        error_log("📝 READABLE TXT BACKUP SAVED: " . $txt_file);
    } else {
        error_log("❌ TXT BACKUP FAILED");
    }
}

// UPLOADS PATH
$uploads_base_path = '../../uploads/';
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Confirmation - Fortunate 51 Scholarship</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* A4 SIZE DESIGN - PERFECT READABILITY WITH SINGLE PAGE */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }
            
            body, html {
                background: white !important;
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 12px !important;
                line-height: 1.3 !important;
                font-family: "Arial", "Helvetica", sans-serif !important;
            }
            
            .confirmation-container {
                width: 200mm !important;
                height: 287mm !important;
                margin: 5mm auto !important;
                padding: 0 !important;
                border: 1.5px solid #3e2723 !important;
                background: white !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
                position: relative !important;
            }
            
            .no-print { 
                display: none !important; 
            }
            
            /* HEADER OPTIMIZATION */
            .header-bg {
                background: #7a0f0f !important;
                color: white !important;
                padding: 8px 0 !important;
                text-align: center !important;
                border-bottom: 3px solid #800000 !important;
                height: 28mm !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            
            .glass-header {
                width: 95% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                padding: 10px 15px !important;
                background: rgba(255, 255, 255, 0.15) !important;
                border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
                border-radius: 12px !important;
            }
            
            .logo {
                width: 70px !important;
                height: 70px !important;
                border: 2px solid #800000 !important;
                border-radius: 6px !important;
                padding: 2px !important;
                background: white !important;
                flex-shrink: 0 !important;
            }
            
            .header-text h1 {
                font-size: 18px !important;
                font-weight: bold !important;
                text-transform: uppercase !important;
                margin: 0 !important;
                line-height: 1.3 !important;
                letter-spacing: 0.5px !important;
            }
            
            .header-text h3 {
                font-size: 16px !important;
                font-weight: bold !important;
                margin: 2px 0 0 0 !important;
                line-height: 1.3 !important;
            }
            
            .header-text h2 {
                font-size: 14px !important;
                font-weight: 600 !important;
                margin: 3px 0 0 0 !important;
                line-height: 1.3 !important;
            }
            
            /* CONTENT AREA */
            .p-4 {
                padding: 8mm !important;
                height: calc(287mm - 28mm) !important;
                overflow: hidden !important;
            }
            
            /* ALERT BOXES */
            .warning-box {
                background: #fff3cd !important;
                border: 1.5px solid #ffeaa7 !important;
                padding: 6px 8px !important;
                margin: 4px 0 5px 0 !important;
                font-size: 11px !important;
                border-left: 4px solid #ffc107 !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                border-radius: 4px !important;
            }
            
            .info-box {
                background: #e8f4f8 !important;
                border: 1.5px solid #bee5eb !important;
                padding: 6px 8px !important;
                margin: 4px 0 6px 0 !important;
                font-size: 11px !important;
                border-left: 4px solid #003366 !important;
                text-align: center !important;
                border-radius: 4px !important;
            }
            
            /* SECTION HEADERS */
            .section-header {
                background: #800000 !important;
                color: white !important;
                padding: 6px 10px !important;
                font-weight: bold !important;
                margin: 8px 0 4px 0 !important;
                font-size: 13px !important;
                border-left: 5px solid #003366 !important;
                border-radius: 3px !important;
            }
            
            /* TABLES - BETTER READABILITY */
            .compact-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-bottom: 6px !important;
                font-size: 11px !important;
                table-layout: fixed !important;
            }
            
            .compact-table td {
                padding: 5px 6px !important;
                border: 1px solid #cccccc !important;
                vertical-align: top !important;
                font-size: 11px !important;
                height: 18px !important;
                line-height: 1.2 !important;
                overflow: hidden !important;
                word-wrap: break-word !important;
            }
            
            .compact-table .label {
                background: #f8f9fa !important;
                font-weight: bold !important;
                width: 28% !important;
                color: #3e2723 !important;
            }
            
            /* PHOTO SECTION */
            .photo-container {
                display: flex !important;
                justify-content: space-around !important;
                margin: 8px 0 6px 0 !important;
                padding: 0 20px !important;
            }
            
            .photo-box {
                text-align: center !important;
                flex: 1 !important;
                max-width: 100px !important;
            }
            
            .photo-placeholder {
                width: 85px !important;
                height: 100px !important;
                border: 1.5px solid #3e2723 !important;
                background: #f9f9f9 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin-bottom: 4px !important;
                border-radius: 3px !important;
            }
            
            .photo-box p {
                font-size: 11px !important;
                color: #3e2723 !important;
                font-weight: bold !important;
                margin: 0 !important;
            }
            
            /* DECLARATION */
            .declaration-text {
                text-align: justify !important;
                font-size: 11px !important;
                line-height: 1.4 !important;
                padding: 8px 10px !important;
                background: #f8f9fa !important;
                border: 1px solid #ddd !important;
                margin-bottom: 8px !important;
                border-left: 4px solid #800000 !important;
                height: 42mm !important;
                overflow: hidden !important;
                border-radius: 3px !important;
            }
            
            .declaration-text ul {
                margin-left: 16px !important;
                margin-top: 4px !important;
            }
            
            .declaration-text li {
                margin-bottom: 2px !important;
                line-height: 1.3 !important;
            }
            
            /* FOOTER */
            .footer-section {
                border-top: 1.5px solid #3e2723 !important;
                padding: 6px 8px !important;
                margin-top: 6px !important;
                background: #f5f5f5 !important;
                font-size: 10px !important;
                position: absolute !important;
                bottom: 8mm !important;
                left: 8mm !important;
                right: 8mm !important;
                border-radius: 3px !important;
            }
            
            .footer-section p {
                margin: 2px 0 !important;
                line-height: 1.3 !important;
            }
            
            /* SPECIFIC TEXT STYLING FOR BETTER READABILITY */
            .text-brown-800 {
                color: #5a3828 !important;
                font-weight: bold !important;
            }
            
            .text-blue-700 {
                color: #1e40af !important;
                font-weight: bold !important;
            }
            
            .text-red-600 {
                color: #dc2626 !important;
                font-weight: bold !important;
            }
            
            .text-blue-900 {
                color: #1e3a8a !important;
                font-weight: bold !important;
            }
        }

        /* SCREEN STYLING - ORIGINAL DESIGN */
        @media screen {
            body {
                background: #f0f0f0;
                padding: 20px;
            }
            .confirmation-container {
                max-width: 210mm;
                margin: 0 auto;
                border: 2px solid #3e2723;
                background: white;
                font-family: Arial, sans-serif;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            }
            .compact-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 8px;
            }
            .compact-table td {
                padding: 6px 8px;
                border: 1px solid #ddd;
                vertical-align: top;
                font-size: 13px;
                height: 24px;
            }
            .compact-table .label {
                background: #f8f9fa;
                font-weight: bold;
                width: 28%;
                color: #3e2723;
            }
            .header-bg {
                background: #7a0f0f;
                color: white;
                padding: 15px;
                text-align: center;
                border-bottom: 3px solid #800000;
            }
            .glass-header {
                width: 95%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 15px 20px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1.5px solid rgba(255, 255, 255, 0.3);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            }
            .section-header {
                background: #800000;
                color: white;
                padding: 8px 12px;
                font-weight: bold;
                margin: 10px 0 5px 0;
                border-left: 4px solid #003366;
            }
            .warning-box {
                background: #fff3cd;
                border: 2px solid #ffeaa7;
                padding: 8px;
                margin: 8px 0;
                border-radius: 4px;
                border-left: 4px solid #ffc107;
            }
            .info-box {
                background: #e8f4f8;
                border: 2px solid #bee5eb;
                padding: 8px;
                margin: 8px 0;
                border-radius: 4px;
                border-left: 4px solid #003366;
            }
            .photo-container {
                display: flex;
                justify-content: space-between;
                gap: 25px;
                margin: 12px 0;
                padding: 0 50px;
            }
            .photo-box {
                text-align: center;
                flex: 1;
                max-width: 120px;
            }
            .photo-placeholder {
                width: 100px;
                height: 120px;
                border: 2px solid #3e2723;
                background: #f9f9f9;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 5px;
                border-radius: 4px;
            }
            .declaration-text {
                text-align: justify;
                font-size: 11px;
                line-height: 1.4;
                padding: 10px;
                background: #f8f9fa;
                border: 1px solid #ddd;
                margin-bottom: 10px;
                border-radius: 4px;
                border-left: 4px solid #800000;
            }
            .footer-section {
                border-top: 2px solid #3e2723;
                padding: 10px;
                margin-top: 15px;
                background: #f5f5f5;
                border-radius: 0 0 4px 4px;
            }
            .logo {
                width: 83px;
                height: 83px;
                border: 2px solid #800000;
                border-radius: 8px;
                padding: 3px;
                background: white;
            }
            
            .header-text h1 {
                font-size: 18px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .header-text h3 {
                font-size: 16px;
                font-weight: bold;
            }
            .header-text h2 {
                font-size: 14px;
                font-weight: 600;
                margin-top: 4px;
            }
        }
        
        .header-bg {
            background-color: #7a0f0f;
            display: flex;
            justify-content: center;
        }

        @media (max-width: 768px){
            .logo {
                width: 50px;
                height: 50px;
            }
            .glass-header {
                padding: 10px;
                flex-wrap: wrap;
                text-align: center;
            }
            .photo-container {
                padding: 0 20px !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="confirmation-container">
        <!-- Header with Logos -->
        <div class="header-bg">
            <div class="glass-header">
                <div class="logo">
                    <img src="../../images/bvgLogo.png" alt="BVG Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="text-center flex-1 mx-4 header-text">
                    <h1>BHAKTIVEDANTA GURUKUL</h1>
                    <h3>School of Excellence</h3>
                    <h3>FORTUNATE 51 Scholarship - Registration Confirmation Page</h3>
                    <!--<h2>Registration Confirmation Page</h2>-->
                </div>
                <div class="logo">
                    <img src="../../images/right-logo.png" alt="BVG Header" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
            </div>
        </div>

        <!-- Application Info -->
        <div class="p-4">
            <div class="warning-box">
                <div class="flex items-center">
                    <span class="font-bold mr-2 text-brown-800">Application No.</span>
                    <span class="font-bold text-blue-700"><?= htmlspecialchars($student_data['unique_id'] ?? $_GET['order_id']) ?></span>
                </div>
                <div class="text-red-600 font-semibold">
                    DO NOT SEND THIS PAGE TO BHAKTIVEDANTA GURUKUL.
                </div>
            </div>
            
            <div class="info-box">
                <p class="text-blue-900">CANDIDATE IS REQUESTED TO RETAIN THE PRINTOUT OF CONFIRMATION PAGE FOR FUTURE REFERENCE.</p>
            </div>

            <!-- Personal Details -->
            <div class="section-header">PERSONAL DETAILS</div>
            <table class="compact-table">
                <tr>
                    <td class="label">Candidate's Name</td>
                    <td><strong><?= htmlspecialchars($student_data['name'] ?? 'N/A') ?></strong></td>
                    <td class="label">Date of Birth</td>
                    <td><strong><?= isset($student_data['dob']) ? date('d/m/Y', strtotime($student_data['dob'])) : 'N/A' ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Father's Name</td>
                    <td><strong><?= htmlspecialchars($student_data['father_name'] ?? 'N/A') ?></strong></td>
                    <td class="label">Gender</td>
                    <td><strong><?= htmlspecialchars($student_data['gender'] ?? 'N/A') ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Mother's Name</td>
                    <td><strong><?= htmlspecialchars($student_data['mother_name'] ?? 'N/A') ?></strong></td>
                    <td class="label">Class</td>
                    <td><strong>Class <?= htmlspecialchars($student_data['class'] ?? 'N/A') ?></strong></td>
                </tr>
                <tr>
                    <td class="label">School Name</td>
                    <td colspan="3"><strong><?= htmlspecialchars($student_data['school_name'] ?? 'N/A') ?></strong></td>
                </tr>
            </table>

            <!-- Contact Details -->
            <div class="section-header">CONTACT DETAILS</div>
            <table class="compact-table">
                <tr>
                    <td class="label">Phone Number</td>
                    <td><strong><?= htmlspecialchars($student_data['phone'] ?? $student_data['contact'] ?? 'N/A') ?></strong></td>
                    <td class="label">Alternate Phone</td>
                    <td><strong><?= htmlspecialchars($student_data['alt_contact'] ?? 'N/A') ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td><strong><?= htmlspecialchars($student_data['email'] ?? 'N/A') ?></strong></td>
                    <td class="label">Aadhaar Number</td>
                    <td><strong><?= htmlspecialchars($student_data['aadhaar'] ?? 'N/A') ?></strong></td>
                </tr>
            </table>

            <!-- Address Information -->
            <div class="section-header">ADDRESS INFORMATION</div>
            <table class="compact-table">
                <tr>
                    <td class="label">House No. & Street</td>
                    <td colspan="3"><strong><?= htmlspecialchars($student_data['address'] ?? 'N/A') ?></strong></td>
                </tr>
                <?php if (!empty($student_data['landmark'])): ?>
                    <tr>
                        <td class="label">Landmark</td>
                        <td colspan="3"><strong><?= htmlspecialchars($student_data['landmark']) ?></strong></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="label">City</td>
                    <td><strong><?= htmlspecialchars($student_data['city'] ?? 'N/A') ?></strong></td>
                    <td class="label">District</td>
                    <td><strong><?= htmlspecialchars($student_data['district'] ?? 'N/A') ?></strong></td>
                </tr>
                <tr>
                    <td class="label">State</td>
                    <td><strong><?= htmlspecialchars($student_data['state'] ?? 'N/A') ?></strong></td>
                    <td class="label">Pincode</td>
                    <td><strong><?= htmlspecialchars($student_data['pincode'] ?? 'N/A') ?></strong></td>
                </tr>
            </table>

            <!-- Payment Details -->
            <div class="section-header">PAYMENT DETAILS</div>
            <table class="compact-table">
                <tr>
                    <td class="label">Transaction ID</td>
                    <td><strong><?= $student_data['phonepe_transaction_id'] ?? $response['orderId'] ?? 'N/A' ?></strong></td>
                    <td class="label">Amount Paid</td>
                    <td style="color: #059669; font-weight: bold; font-size: 12px;">₹<?= $student_data['amount'] ?? (($response['amount'] ?? 0) / 100) ?></td>
                </tr>
                <tr>
                    <td class="label">Payment Date</td>
                    <td><strong><?= date('d/m/Y h:i A') ?></strong></td>
                    <td class="label">Payment Method</td>
                    <td><strong><?= $student_data['payment_method'] ?? ($response['paymentMethod'] ?? 'PHONEPE') ?></strong></td>
                </tr>
            </table>

            <!-- Images -->
            <div class="section-header">IMAGES UPLOADED BY CANDIDATE</div>
            <div class="photo-container">
                <div class="photo-box">
                    <div class="photo-placeholder">
                        <?php if (!empty($student_data['photo'])): ?>
                            <img src="<?= $uploads_base_path . htmlspecialchars($student_data['photo']) ?>"
                                alt="Student Photo"
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 2px;">
                        <?php else: ?>
                            <span style="color: #666; font-size: 10px;">Photograph</span>
                        <?php endif; ?>
                    </div>
                    <p>Photograph</p>
                </div>
                <div class="photo-box">
                    <div class="photo-placeholder">
                        <?php if (!empty($student_data['sign'])): ?>
                            <img src="<?= $uploads_base_path . htmlspecialchars($student_data['sign']) ?>"
                                alt="Student Signature"
                                style="width: 100%; height: 100%; object-fit: contain; border-radius: 2px;">
                        <?php else: ?>
                            <span style="color: #666; font-size: 10px;">Signature</span>
                        <?php endif; ?>
                    </div>
                    <p>Signature</p>
                </div>
            </div>

            <!-- Declaration -->
            <div class="section-header">DECLARATION</div>
            <div class="declaration-text">
                I hereby declare that all the particulars given by me in this form are true to the best of my knowledge and belief and any mistake / misinformation, detected at the time of admission or at any stage in future, will result in the cancellation of admission/candidature. I have read the information bulletin and understood all the procedures. In case I furnish any false information, my result will not be declared; my candidature will automatically stand cancelled. I shall abide by terms and conditions therein. No candidate should adopt any unfair means, or indulge in any unfair examination practices. If at any stage, it is found that the candidate has submitted multiple applications, then the candidature will be cancelled and legal action will be taken.

                <br><br>

                <ul>
                    <li>Further details related to this Scholarship exam will be communicated via your registered email.</li>
                    <li>Please keep checking your email regularly to stay updated regarding the examination process.</li>
                </ul>
            </div>

            <!-- Footer -->
            <div class="footer-section">
                <p><strong style="color: #3e2723;">List of Document Uploaded:</strong> Photograph, Signature</p>
                <p><strong style="color: #3e2723;">IP Address:</strong> <?= $_SERVER['REMOTE_ADDR'] ?? 'N/A' ?></p>
                <p style="text-align: center; color: #003366; font-weight: bold;">
                    <strong>Date of Downloading:</strong> <?= date('d/m/Y h:i:s A') ?>
                </p>
            </div>
        </div>

        <!-- Action Buttons (Screen Only) -->
        <div class="no-print p-4 border-t bg-gray-50 text-center">
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button onclick="perfectPrint()" class="inline-flex items-center justify-center px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">
                    <i class="fas fa-print mr-2"></i>Print Confirmation
                </button>
                <a href="<?= APP_URL ?>/preview.php" class="inline-flex items-center justify-center px-4 py-2 rounded bg-gray-600 hover:bg-gray-700 text-white font-semibold text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Application
                </a>
                <a href="<?= APP_URL ?>/../../index.php" class="inline-flex items-center justify-center px-4 py-2 rounded border border-green-600 text-green-700 hover:bg-green-50 font-semibold text-sm">
                    <i class="fas fa-home mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        function perfectPrint() {
            // Print dialog open karega
            window.print();
        }
        
        // Page load par check karega ki print ho raha hai ya nahi
        window.addEventListener('afterprint', function() {
            console.log('Print completed or cancelled');
        });
    </script>
</body>
</html>