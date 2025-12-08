<?php
session_start();

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/config.php';

// Load PhonePe Helper
require_once __DIR__ . '/PhonePeHelper.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate session or required data
        if (!isset($_SESSION['admission_id']) && !isset($_POST['order_id'])) {
            throw new Exception("Admission session or order ID not found. Please submit the admission form first.");
        }

        // Generate or validate order_id
        if (empty($_POST['order_id'])) {
            // Use admission_id from session if available
            if (isset($_SESSION['admission_id'])) {
                $order_id = 'BVG_KHARGONE_' . $_SESSION['admission_id'] . '_' . time();
            } else {
                $order_id = 'BVG_KHARGONE_' . time() . rand(1000, 9999);
            }
            // Store order_id in session for verification later
            $_SESSION['payment_order_id'] = $order_id;
        } else {
            $order_id = $_POST['order_id'];
            // Also store in session
            $_SESSION['payment_order_id'] = $order_id;
        }

        // CORRECT AMOUNT CALCULATION: ₹500 = 50000 paise
        $amount_in_rupees = 500; // Fixed application fee for Khargone campus
        $amount_in_paise = $amount_in_rupees * 100; // Convert to paise
        
        // Validate amount (minimum ₹1 = 100 paise)
        if ($amount_in_paise < 100) {
            $amount_in_paise = 50000; // Default to ₹500 if invalid
        }

        // Prepare response path with additional parameters
        $response_path = RESPONSE_PATH . "?order_id=" . urlencode($order_id) . 
                        "&admission_id=" . (isset($_SESSION['admission_id']) ? $_SESSION['admission_id'] : '') .
                        "&campus=khargone";

        // Initialize PhonePe Helper
        $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, ENV);
        
        // Create payment request
        $response = $phonePeHelper->createPayment($order_id, $amount_in_paise, $response_path);

        // Log payment initiation (for debugging)
        error_log("Payment initiated - Order ID: $order_id, Amount: ₹$amount_in_rupees ($amount_in_paise paise)");

        // Store payment details in session
        $_SESSION['payment_details'] = [
            'order_id' => $order_id,
            'amount' => $amount_in_paise,
            'amount_in_rupees' => $amount_in_rupees,
            'timestamp' => time(),
            'campus' => 'khargone'
        ];

        // Validate response
        if (!isset($response['redirectUrl']) || empty($response['redirectUrl'])) {
            throw new Exception("Payment gateway returned invalid response. Please try again.");
        }

        // Redirect to PhonePe payment page
        echo "<script>
            // Show loading message
            document.body.innerHTML = '<div style=\"text-align:center; padding:50px; font-family:Arial;\">
                <h2 style=\"color:#003366;\">Redirecting to Payment Gateway...</h2>
                <p>Please wait while we redirect you to the secure payment page.</p>
                <p><i>Application Fee: ₹$amount_in_rupees</i></p>
                <div style=\"margin:20px;\">
                    <div class=\"spinner\"></div>
                </div>
                <p>If you are not redirected automatically, <a href=\"' + '" . $response['redirectUrl'] . "' + '\">click here</a></p>
            </div>';
            
            // Add spinner CSS
            var style = document.createElement('style');
            style.innerHTML = '.spinner { border: 5px solid #f3f3f3; border-top: 5px solid #003366; border-radius: 50%; width: 50px; height: 50px; animation: spin 2s linear infinite; margin: 0 auto; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
            document.head.appendChild(style);
            
            // Redirect after 2 seconds
            setTimeout(function() {
                window.location.href = '" . $response['redirectUrl'] . "';
            }, 2000);
        </script>";
        
        exit;

    } catch (Exception $e) {
        // Log error
        error_log("Payment Error: " . $e->getMessage());
        
        // User-friendly error message
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Payment Error</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                    min-height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin: 0;
                    padding: 20px;
                }
                .error-container {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                    padding: 40px;
                    max-width: 500px;
                    text-align: center;
                }
                .error-icon {
                    color: #e74c3c;
                    font-size: 48px;
                    margin-bottom: 20px;
                }
                h1 {
                    color: #333;
                    margin-bottom: 20px;
                }
                .error-message {
                    color: #666;
                    margin-bottom: 30px;
                    line-height: 1.6;
                }
                .button-group {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                }
                .btn {
                    padding: 12px 24px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    font-weight: bold;
                    text-decoration: none;
                    display: inline-block;
                    transition: all 0.3s ease;
                }
                .btn-primary {
                    background: linear-gradient(135deg, #003366 0%, #004080 100%);
                    color: white;
                }
                .btn-primary:hover {
                    background: linear-gradient(135deg, #002244 0%, #003366 100%);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 15px rgba(0, 51, 102, 0.3);
                }
                .btn-secondary {
                    background: #f8f9fa;
                    color: #333;
                    border: 1px solid #dee2e6;
                }
                .btn-secondary:hover {
                    background: #e9ecef;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                }
                .contact-info {
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #eee;
                    color: #666;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class='error-container'>
                <div class='error-icon'>⚠️</div>
                <h1>Payment Processing Error</h1>
                <div class='error-message'>
                    <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                    <p>We encountered an issue while processing your payment. Please try again or contact support if the problem persists.</p>
                </div>
                <div class='button-group'>
                    <a href='../khargone-form.php' class='btn btn-primary'>← Go Back to Admission Form</a>
                    <a href='" . $base_url . "/index.php' class='btn btn-secondary'>🏠 Go to Homepage</a>
                </div>
                <div class='contact-info'>
                    <p>Need help? Contact our admissions team:</p>
                    <p>📞 +91-7618040040 | 📧 admissions@khargonecampus.edu</p>
                </div>
            </div>
        </body>
        </html>";
        exit;
    }
} else {
    // If not POST request, redirect to home page
    header("Location: " . $base_url . "/index.php");
    exit;
}
?>