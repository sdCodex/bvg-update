<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Load configuration
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/PhonePeHelper.php';

// Database connection
require_once __DIR__ . '/../../../includes/db.php';

$payment_status = '';
$payment_data = [];
$error_message = '';
$success_message = '';
$redirect_url = '';
$admission_id = $_SESSION['admission_id'] ?? '';

try {
    // VALIDATE order_id
    if (empty($_GET['order_id'])) {
        throw new Exception("No Transaction ID found in URL. Please complete your payment.");
    }
    
    $order_id = $_GET['order_id'];
    
    // Initialize PhonePe Helper
    $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, ENV);
    
    // Check payment status
    $response = $phonePeHelper->checkPaymentStatus($order_id);
    
    // Extract payment details
    $payment_data = $response;
    $payment_state = $response['state'] ?? 'UNKNOWN';
    $payment_amount = isset($response['amount']) ? ($response['amount'] / 100) : 0; // Convert paise to rupees
    
    // Check if payment is completed
    if ($payment_state === 'COMPLETED') {
        $payment_status = 'success';
        
        // Update database with payment success
        $update_sql = "UPDATE khargone_admissions 
                      SET payment_status = 'completed', 
                          payment_id = :payment_id,
                          payment_amount = :amount,
                          payment_date = NOW(),
                          status = 'payment_completed'
                      WHERE id = :admission_id 
                      AND (payment_id IS NULL OR payment_id = :payment_id2)";
        
        $stmt = $pdo->prepare($update_sql);
        $stmt->execute([
            ':payment_id' => $order_id,
            ':amount' => $payment_amount,
            ':admission_id' => $admission_id,
            ':payment_id2' => $order_id
        ]);
        
        if ($stmt->rowCount() > 0) {
            $success_message = "Payment of ₹{$payment_amount} completed successfully!";
            
            // Store in session
            $_SESSION['payment_success'] = true;
            $_SESSION['payment_details'] = [
                'order_id' => $order_id,
                'amount' => $payment_amount,
                'status' => 'completed',
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // Prepare success email or notification
            // You can add email sending logic here
            
        } else {
            // Payment already recorded
            $success_message = "Payment already recorded. Thank you!";
        }
        
    } elseif (in_array($payment_state, ['PENDING', 'FAILED', 'CANCELLED'])) {
        $payment_status = 'failed';
        
        // Prepare for retry
        $amount_in_paise = 50000; // ₹500 in paise
        $response_path = RESPONSE_PATH . "?order_id=" . urlencode($order_id);
        
        // Generate new order ID for retry
        $new_order_id = 'BVG_RETRY_' . $order_id . '_' . time();
        
        $retry_response = $phonePeHelper->createPayment($new_order_id, $amount_in_paise, $response_path);
        $redirect_url = $retry_response['redirectUrl'] ?? '';
        
        $error_message = "Payment is {$payment_state}. Please try again.";
        
        // Update database with failed status
        $update_sql = "UPDATE khargone_admissions 
                      SET payment_status = :status, 
                          payment_note = :note
                      WHERE id = :admission_id";
        
        $stmt = $pdo->prepare($update_sql);
        $stmt->execute([
            ':status' => $payment_state,
            ':note' => 'Payment failed, retry initiated',
            ':admission_id' => $admission_id
        ]);
        
    } else {
        $payment_status = 'unknown';
        $error_message = "Payment status is {$payment_state}. Please contact support.";
    }
    
} catch (Exception $e) {
    $payment_status = 'error';
    $error_message = "Error: " . htmlspecialchars($e->getMessage());
    
    // Log error
    error_log("Payment Response Error: " . $e->getMessage() . " | Order ID: " . ($_GET['order_id'] ?? 'N/A'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status - Khargone Campus Admission</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #D4AF37;
            --accent-color: #800000;
            --success-color: #10b981;
            --error-color: #ef4444;
        }
        
        body { 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 32px;
        }
        
        .status-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: var(--success-color);
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #f59e0b;
        }
        
        .status-failed {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: var(--error-color);
        }
        
        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center p-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-[#003366] to-[#800000] rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Khargone Campus Admission</h1>
            </div>
            <p class="text-gray-600">Payment Status</p>
        </div>
        
        <!-- Main Card -->
        <div class="w-full max-w-2xl card rounded-2xl overflow-hidden transition-all duration-500">
            <!-- Status Header -->
            <div class="bg-gradient-to-r from-[#003366] to-[#004080] p-6 text-white text-center">
                <div class="relative">
                    <?php if ($payment_status === 'success'): ?>
                        <div class="status-icon status-success mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 class="text-2xl font-bold">Payment Successful!</h2>
                        <p class="text-blue-100 mt-2">Your admission application fee has been paid</p>
                        
                    <?php elseif ($payment_status === 'failed'): ?>
                        <div class="status-icon status-failed mb-4">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <h2 class="text-2xl font-bold">Payment Failed</h2>
                        <p class="text-blue-100 mt-2">We couldn't process your payment</p>
                        
                    <?php elseif ($payment_status === 'unknown'): ?>
                        <div class="status-icon status-pending mb-4">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h2 class="text-2xl font-bold">Payment Status Unknown</h2>
                        <p class="text-blue-100 mt-2">Please verify your payment status</p>
                        
                    <?php else: ?>
                        <div class="animate-pulse">
                            <div class="w-20 h-20 bg-white/20 rounded-full mx-auto mb-4"></div>
                        </div>
                        <h2 class="text-2xl font-bold">Processing Payment...</h2>
                        <p class="text-blue-100 mt-2">Please wait while we verify your payment</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6 md:p-8">
                <!-- Messages -->
                <?php if ($success_message): ?>
                    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 animate-fade-in">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-green-800 font-semibold"><?php echo $success_message; ?></p>
                                <p class="text-green-600 text-sm mt-1">Your admission application is now complete.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4 animate-fade-in">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-red-800 font-semibold"><?php echo $error_message; ?></p>
                                <p class="text-red-600 text-sm mt-1">Please try the payment again or contact support.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Payment Details -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-receipt mr-2 text-[#003366]"></i>
                        Payment Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Order Details -->
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-4 border border-blue-100">
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Order ID</div>
                            <div class="font-mono text-gray-800 font-semibold break-all"><?php echo htmlspecialchars($order_id ?? 'N/A'); ?></div>
                        </div>
                        
                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl p-4 border border-purple-100">
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Status</div>
                            <div class="font-semibold text-gray-800 flex items-center">
                                <?php if ($payment_state === 'COMPLETED'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Completed</span>
                                <?php elseif ($payment_state === 'PENDING'): ?>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">Pending</span>
                                <?php elseif ($payment_state === 'FAILED'): ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">Failed</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm"><?php echo htmlspecialchars($payment_state); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Amount -->
                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-4 border border-amber-100">
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Amount Paid</div>
                            <div class="text-2xl font-bold text-gray-800">₹<?php echo number_format($payment_amount ?? 0, 2); ?></div>
                            <div class="text-xs text-gray-500 mt-1">Application Fee</div>
                        </div>
                        
                        <!-- Admission ID -->
                        <?php if ($admission_id): ?>
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Admission Reference</div>
                            <div class="font-semibold text-gray-800">KHC-<?php echo str_pad($admission_id, 6, '0', STR_PAD_LEFT); ?></div>
                            <div class="text-xs text-gray-500 mt-1">Keep this for future reference</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Additional Information -->
                <?php if ($payment_state === 'COMPLETED'): ?>
                    <div class="mb-8 bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-[#003366]"></i>
                            Next Steps
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                <span>You will receive a confirmation email shortly</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-file-alt text-blue-500 mt-1 mr-3"></i>
                                <span>Our admissions team will contact you for document verification</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-calendar-check text-purple-500 mt-1 mr-3"></i>
                                <span>Schedule a campus visit to complete the admission process</span>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6 border-t border-gray-200">
                    <?php if ($payment_state === 'COMPLETED'): ?>
                        <a href="<?php echo $base_url; ?>/index.php" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#003366] to-[#004080] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fas fa-home mr-2"></i>
                            Go to Homepage
                        </a>
                        <a href="<?php echo $base_url; ?>/pages/admissions/index.php" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-white border border-[#003366] text-[#003366] font-semibold rounded-xl hover:bg-[#003366] hover:text-white transition-all duration-300">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            View Admissions
                        </a>
                    <?php elseif ($redirect_url): ?>
                        <a href="<?php echo $redirect_url; ?>" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#800000] to-[#a00000] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-redo mr-2"></i>
                            Try Payment Again
                        </a>
                        <a href="<?php echo $base_url; ?>/pages/admissions/khargone/khargone-form.php" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Form
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>/index.php" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#003366] to-[#004080] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                            <i class="fas fa-home mr-2"></i>
                            Go to Homepage
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Support Info -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600 mb-2">Need help with your payment?</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="tel:+917618040040" class="inline-flex items-center text-sm text-[#003366] hover:text-[#800000]">
                            <i class="fas fa-phone mr-2"></i>
                            +91-7618040040
                        </a>
                        <a href="mailto:admissions@khargonecampus.edu" class="inline-flex items-center text-sm text-[#003366] hover:text-[#800000]">
                            <i class="fas fa-envelope mr-2"></i>
                            admissions@khargonecampus.edu
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Note -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>© <?php echo date('Y'); ?> Khargone Campus. All rights reserved.</p>
            <p class="mt-1">Application Fee: ₹500 (Non-refundable)</p>
        </div>
    </div>
    
    <!-- Auto-redirect for retry -->
    <?php if ($redirect_url && $payment_state === 'PENDING'): ?>
    <script>
        setTimeout(function() {
            window.location.href = "<?php echo $redirect_url; ?>";
        }, 5000); // Redirect after 5 seconds
    </script>
    <?php endif; ?>
    
    <!-- Print receipt on success -->
    <?php if ($payment_state === 'COMPLETED'): ?>
    <script>
        window.onload = function() {
            // Auto-print option (uncomment if needed)
            // setTimeout(function() { window.print(); }, 2000);
        }
    </script>
    <?php endif; ?>
</body>
</html>