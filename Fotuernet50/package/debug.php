<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/mailer.php';

// ✅ PHPMailer Include Karein - YAHAN ADD KARO
require_once __DIR__ . '/vendor/autoload.php'; // Ya aapka correct path
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// ✅ CSV PROCESSING FUNCTIONS START
function saveBackupAndCSV($student_data, $transaction_id, $amount, $payment_method, $response)
{
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
            error_log("📄 ALREADY PROCESSED - SKIPPING: " . $student_data['unique_id']);
            return true;
        }
    }

    // ✅ SESSION TRACKING CHECK
    if (
        isset($_SESSION['backup_processed_' . $student_data['unique_id']]) &&
        $_SESSION['backup_processed_' . $student_data['unique_id']] === true
    ) {
        error_log("📄 ALREADY PROCESSED (SESSION) - SKIPPING: " . $student_data['unique_id']);
        return true;
    }

    $success = true;

    // ✅ 1. PEHLE JSON BACKUP
    if (!saveJsonBackup($student_data, $response, $transaction_id, $amount, $payment_method, $backup_dir)) {
        $success = false;
        error_log("❌ JSON BACKUP FAILED");
    }

    // ✅ 2. PHIR TXT BACKUP
    if (!saveTxtBackup($student_data, $transaction_id, $amount, $payment_method, $backup_dir)) {
        $success = false;
        error_log("❌ TXT BACKUP FAILED");
    }

    // ✅ 3. PHIR CSV ENTRIES
    if (!saveSingleCSVEntry($student_data, $transaction_id, $amount, $payment_method, $backup_dir)) {
        $success = false;
        error_log("❌ CSV ENTRY FAILED");
    }

    // ✅ 4. COMPREHENSIVE CSV
    if (!saveComprehensiveCSV($student_data, $transaction_id, $amount, $payment_method, $backup_dir)) {
        $success = false;
        error_log("❌ COMPREHENSIVE CSV FAILED");
    }

    // ✅ AGAR SAB SUCCESS TO TRACK MARK KARO
    if ($success) {
        // FILE TRACKING
        file_put_contents($tracker_file, $student_data['unique_id'] . PHP_EOL, FILE_APPEND | LOCK_EX);

        // SESSION TRACKING
        $_SESSION['backup_processed_' . $student_data['unique_id']] = true;

        error_log("🎊 ALL BACKUPS & CSV PROCESSED SUCCESSFULLY: " . $student_data['unique_id']);
    }

    return $success;
}

function saveJsonBackup($student_data, $response, $transaction_id, $amount, $payment_method, $backup_dir)
{
    error_log("💾 STARTING JSON BACKUP...");

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

    // JSON file mein save karo
    $filename = $backup_dir . $student_data['unique_id'] . '.json';
    if (file_put_contents($filename, json_encode($backup_data, JSON_PRETTY_PRINT))) {
        error_log("✅ JSON BACKUP SAVED: " . $filename);
        return true;
    } else {
        error_log("❌ JSON BACKUP FAILED");
        return false;
    }
}

function saveTxtBackup($student_data, $transaction_id, $amount, $payment_method, $backup_dir)
{
    $txt_file = $backup_dir . $student_data['unique_id'] . '.txt';

    $content = "=============================================\n";
    $content .= "Fortunate 51 SCHOLARSHIP - REGISTRATION CONFIRMATION\n";
    $content .= "=============================================\n\n";

    $content .= "REGISTRATION DETAILS:\n";
    $content .= "=====================\n";
    $content .= "Registration ID: " . $student_data['unique_id'] . "\n";
    $content .= "Transaction ID: " . $transaction_id . "\n";
    $content .= "Name: " . $student_data['name'] . "\n";
    $content .= "Father's Name: " . $student_data['father_name'] . "\n";
    $content .= "Mother's Name: " . $student_data['mother_name'] . "\n";
    $content .= "Date of Birth: " . $student_data['dob'] . "\n";
    $content .= "Gender: " . $student_data['gender'] . "\n";
    $content .= "Aadhaar: " . $student_data['aadhaar'] . "\n\n";

    $content .= "CONTACT INFORMATION:\n";
    $content .= "====================\n";
    $content .= "Phone: " . $student_data['phone'] . "\n";
    $content .= "Alternate Phone: " . $student_data['alt_contact'] . "\n";
    $content .= "Email: " . $student_data['email'] . "\n\n";

    $content .= "ACADEMIC DETAILS:\n";
    $content .= "=================\n";
    $content .= "Class: " . $student_data['class'] . "\n";
    $content .= "School: " . $student_data['school_name'] . "\n\n";

    $content .= "ADDRESS:\n";
    $content .= "========\n";
    $content .= "Address: " . $student_data['address'] . "\n";
    $content .= "City: " . $student_data['city'] . "\n";
    $content .= "District: " . $student_data['district'] . "\n";
    $content .= "State: " . $student_data['state'] . "\n";
    $content .= "Pincode: " . $student_data['pincode'] . "\n";
    $content .= "Landmark: " . $student_data['landmark'] . "\n\n";

    $content .= "PAYMENT DETAILS:\n";
    $content .= "================\n";
    $content .= "Transaction ID: " . $transaction_id . "\n";
    $content .= "Amount: ₹" . $amount . "\n";
    $content .= "Payment Method: " . $payment_method . "\n";
    $content .= "Payment Status: success\n";
    $content .= "Payment Date: " . date('Y-m-d H:i:s') . "\n\n";

    $content .= "=============================================\n";
    $content .= "Generated on: " . date('d/m/Y h:i A') . "\n";
    $content .= "=============================================\n";

    if (file_put_contents($txt_file, $content)) {
        error_log("📝 TXT BACKUP SAVED: " . $txt_file);
        return true;
    } else {
        error_log("❌ TXT BACKUP FAILED");
        return false;
    }
}

function saveSingleCSVEntry($student_data, $transaction_id, $amount, $payment_method, $backup_dir)
{
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
        'Timestamp'
    ];

    // Agar file nahi hai to headers add karo
    if (!file_exists($csv_file)) {
        $fp = fopen($csv_file, 'w');
        if ($fp) {
            // UTF-8 BOM add karo for Excel compatibility
            fwrite($fp, "\xEF\xBB\xBF");
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
            date('Y-m-d'),
            date('Y-m-d H:i:s')
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

// ✅ COMPREHENSIVE CSV BACKUP
function saveComprehensiveCSV($student_data, $transaction_id, $amount, $payment_method, $backup_dir)
{
    $csv_file = $backup_dir . 'complete_registrations.csv';

    $csv_headers = [
        'Registration_ID',
        'Transaction_ID',
        'Student_Name',
        'Father_Name',
        'Mother_Name',
        'Gender',
        'DOB',
        'Email',
        'Phone',
        'Alternate_Phone',
        'Aadhaar_Number',
        'Class',
        'School_Name',
        'Address',
        'City',
        'District',
        'State',
        'Pincode',
        'Landmark',
        'Amount',
        'Payment_Method',
        'Payment_Status',
        'Payment_Date',
        'Registration_Date'
    ];

    // Check if file exists
    $file_exists = file_exists($csv_file);

    $fp = fopen($csv_file, 'a');
    if ($fp) {
        // Add headers if file is new
        if (!$file_exists) {
            fwrite($fp, "\xEF\xBB\xBF");
            fputcsv($fp, $csv_headers);
        }

        $csv_data = [
            $student_data['unique_id'],
            $transaction_id,
            $student_data['name'],
            $student_data['father_name'],
            $student_data['mother_name'] ?? 'N/A',
            $student_data['gender'] ?? 'N/A',
            $student_data['dob'] ?? 'N/A',
            $student_data['email'],
            $student_data['phone'],
            $student_data['alt_contact'] ?? 'N/A',
            $student_data['aadhaar'] ?? 'N/A',
            $student_data['class'],
            $student_data['school_name'],
            $student_data['address'] ?? 'N/A',
            $student_data['city'],
            $student_data['district'] ?? 'N/A',
            $student_data['state'],
            $student_data['pincode'] ?? 'N/A',
            $student_data['landmark'] ?? 'N/A',
            $amount,
            $payment_method,
            'success',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s', strtotime($student_data['created_at'] ?? 'now'))
        ];

        fputcsv($fp, $csv_data);
        fclose($fp);
        error_log("📄 COMPREHENSIVE CSV ENTRY ADDED: " . $student_data['unique_id']);
        return true;
    } else {
        error_log("❌ COMPREHENSIVE CSV FILE OPEN FAILED");
        return false;
    }
}
// ✅ CSV PROCESSING FUNCTIONS END

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
        error_log("✅ PAYMENT COMPLETED - PROCESSING STARTED");

        // ✅ EXTRACT PAYMENT DETAILS CORRECTLY
        $transaction_id = $response['transactionId'] ?? $response['paymentDetails'][0]['transactionId'] ?? $response['orderId'] ?? $order_id;
        $amount = ($response['amount'] ?? 0) / 100; // Convert to rupees
        $payment_method = $response['paymentMethod'] ?? $response['paymentDetails'][0]['paymentMode'] ?? 'PHONEPE';
        $merchant_id = $response['merchantId'] ?? '';
        $transaction_code = $response['transactionCode'] ?? $transaction_id;

        error_log("💾 PAYMENT DETAILS:");
        error_log("   - Transaction ID: " . $transaction_id);
        error_log("   - Amount: " . $amount);
        error_log("   - Payment Method: " . $payment_method);

        // ✅ ✅ ✅ PEHLE SAB BACKUP & CSV FILES ✅ ✅ ✅
        error_log("🚀 STARTING ALL BACKUPS & CSV (BEFORE DATABASE)...");

        $backup_result = saveBackupAndCSV(
            $student_data,
            $transaction_id,
            $amount,
            $payment_method,
            $response
        );

        if ($backup_result) {
            error_log("🎊 ALL BACKUPS & CSV FILES CREATED SUCCESSFULLY!");
            
                // ✅ ✅ ✅ ADMIN KO CONFIRMATION EMAIL BHEJO (SUCCESSFUL PAYMENT)
    try {
        $mail = new PHPMailer(true);
        
        // Server settings (SAME AS PREVIEW PAGE)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cmd@ourgurukul.org';
        $mail->Password = 'swdr epfq ffdd fjuk'; // SAME PASSWORD
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('cmd@ourgurukul.org', 'Bhaktivedanta Gurukul Scholarship');
        $mail->addAddress('cmd@ourgurukul.org'); // Admin email
        $mail->addReplyTo($student_data['email'], $student_data['name']); // Student se reply aa sake
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = '✅ PAYMENT SUCCESSFUL - Scholarship Registration: ' . $student_data['unique_id'];
        
        // Email body with all details
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background: #22c55e; color: white; padding: 20px; }
                .content { padding: 20px; background: #f9f9f9; }
                .details { background: white; padding: 15px; border-radius: 5px; }
                .success { color: #22c55e; font-weight: bold; }
                .backup-info { background: #f0f9ff; padding: 10px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>✅ PAYMENT SUCCESSFUL - Scholarship Registration</h2>
                    <p>Registration ID: ' . $student_data['unique_id'] . '</p>
                </div>
                <div class="content">
                    <h3>🎉 Payment Confirmed!</h3>
                    
                    <div class="details">
                        <h4>📋 Student Details:</h4>
                        <p><strong>Name:</strong> ' . htmlspecialchars($student_data['name']) . '</p>
                        <p><strong>Father:</strong> ' . htmlspecialchars($student_data['father_name']) . '</p>
                        <p><strong>Class:</strong> Class ' . htmlspecialchars($student_data['class']) . '</p>
                        <p><strong>School:</strong> ' . htmlspecialchars($student_data['school_name']) . '</p>
                        <p><strong>Phone:</strong> ' . htmlspecialchars($student_data['phone']) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($student_data['email']) . '</p>
                        
                        <h4>💰 Payment Information:</h4>
                        <p class="success"><strong>Status:</strong> SUCCESSFUL ✅</p>
                        <p><strong>Amount:</strong> ₹' . $amount . '.00</p>
                        <p><strong>Transaction ID:</strong> ' . $transaction_id . '</p>
                        <p><strong>Payment Method:</strong> ' . $payment_method . '</p>
                        <p><strong>Payment Time:</strong> ' . date('d/m/Y h:i A') . '</p>
                        
                        <div class="backup-info">
                            <h4>💾 Backup Files Created:</h4>
                            <p>1. JSON Backup: ' . $student_data['unique_id'] . '.json</p>
                            <p>2. TXT Backup: ' . $student_data['unique_id'] . '.txt</p>
                            <p>3. CSV Entry: all_registrations.csv</p>
                            <p>4. Comprehensive CSV: complete_registrations.csv</p>
                        </div>
                        
                        <p><strong>📍 Location:</strong> ' . $backup_dir . '</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->AltBody = "PAYMENT SUCCESSFUL - Scholarship Registration\n" .
                         "=============================================\n" .
                         "Registration ID: " . $student_data['unique_id'] . "\n" .
                         "Student: " . $student_data['name'] . "\n" .
                         "Class: " . $student_data['class'] . "\n" .
                         "School: " . $student_data['school_name'] . "\n" .
                         "Phone: " . $student_data['phone'] . "\n" .
                         "Amount: ₹" . $amount . "\n" .
                         "Transaction ID: " . $transaction_id . "\n" .
                         "Payment Method: " . $payment_method . "\n" .
                         "Time: " . date('d/m/Y h:i A') . "\n" .
                         "Backup files created successfully.\n";
        
        if ($mail->send()) {
            error_log("📧 PAYMENT SUCCESS EMAIL SENT TO ADMIN");
        } else {
            error_log("⚠️ PAYMENT EMAIL FAILED: " . $mail->ErrorInfo);
        }
        
    } catch (Exception $emailError) {
        error_log("💥 EMAIL ERROR: " . $emailError->getMessage());
        // Email fail hua to bhi process continue rahega
    }
        } else {
            error_log("⚠️ SOME BACKUP/CSV FILES FAILED");
        }
        $mailer = new SimpleMailer();
        $mailer->sendBackupToAdmin($student_data, $transaction_id, $amount, $backup_dir);
        // ✅ ✅ ✅ AB DATABASE UPDATE KARO ✅ ✅ ✅
        error_log("💾 STARTING DATABASE UPDATE...");

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

                // ✅ SESSION MESSAGE (Optional)
                if ($payment_success && $backup_result) {
                    error_log("✅ COMPLETE PROCESS SUCCESSFUL!");
                    // $_SESSION['processing_success'] = "Registration completed successfully!";
                }
            } else {
                error_log("❌ DATABASE UPDATE FAILED - No rows affected");

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
            // Database fail hone par bhi backups already saved hain
        }
    } else {
        error_log("❌ PAYMENT NOT COMPLETED - State: " . ($response['state'] ?? 'UNKNOWN'));
    }

    // response.php में backup ke baad email send करें:
    error_log("🚀 STARTING ALL BACKUPS & CSV (BEFORE DATABASE)...");

    $backup_result = saveBackupAndCSV(
        $student_data,
        $transaction_id,
        $amount,
        $payment_method,
        $response
    );

    if ($backup_result) {
        error_log("🎊 ALL BACKUPS & CSV FILES CREATED SUCCESSFULLY!");

        // ✅ EMAIL TO ADMIN
        try {
            $mailer = new SimpleMailer();

            // Admin को backup भेजें
            $email_sent = $mailer->sendBackupToAdmin($student_data, $transaction_id, $amount, $backup_dir);

            if ($email_sent) {
                error_log("📧 EMAIL SENT TO ADMIN SUCCESSFULLY");

                // Student को confirmation भेजें (optional)
                // $mailer->sendConfirmationToStudent($student_data, $transaction_id, $amount);
            } else {
                error_log("⚠️ ADMIN EMAIL FAILED - BUT REGISTRATION CONTINUES");
            }
        } catch (Exception $e) {
            error_log("💥 EMAIL ERROR: " . $e->getMessage());
            // Email fail hone par bhi process continue rahega
        }
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

// UPLOADS PATH
$uploads_base_path = '../../uploads/';
?>
