<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/PhonePeHelper.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
    try {
        // ✅ FIX: Check all possible sources for order_id
        $order_id = '';
        
        if(isset($_POST['order_id']) && !empty($_POST['order_id'])) {
            $order_id = trim($_POST['order_id']);
        } elseif(isset($_GET['order_id']) && !empty($_GET['order_id'])) {
            $order_id = trim($_GET['order_id']);
        } elseif(isset($_SESSION['order_id']) && !empty($_SESSION['order_id'])) {
            $order_id = trim($_SESSION['order_id']);
        } elseif(isset($_SESSION['unique_id']) && !empty($_SESSION['unique_id'])) {
            $order_id = trim($_SESSION['unique_id']);
        }
        
        // Validate order_id
        if(empty($order_id)) {
            // Show user-friendly error
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Payment Error</title>
                <script src="https://cdn.tailwindcss.com"></script>
            </head>
            <body class="bg-gray-100 flex items-center justify-center min-h-screen">
                <div class="bg-white p-8 rounded-lg shadow-md text-center">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Order ID Not Found</h2>
                    <p class="text-gray-600 mb-4">Unable to process payment. Please try again.</p>
                    <a href="/khargone.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Go Back to Form</a>
                </div>
            </body>
            </html>
            <?php
            exit;
        }
        
        // ✅ FIX: CORRECT AMOUNT - ₹500 = 50000 paise
        $amount = 50000; // ₹500 = 50000 paise
        
        $response_path = RESPONSE_PATH . "?order_id=" . urlencode($order_id);
        
        $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, ENV);
        $response = $phonePeHelper->createPayment($order_id, $amount, $response_path);
        
        // Check if redirect URL is available
        if(isset($response['redirectUrl']) && !empty($response['redirectUrl'])) {
            // Direct redirect (better than JavaScript)
            header("Location: " . $response['redirectUrl']);
            exit;
        } else {
            throw new Exception("Payment gateway did not return redirect URL");
        }
        
    } catch (Throwable $e) {
        // Better error display
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Error</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Payment Error</h2>
                <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($e->getMessage()); ?></p>
                <p class="text-sm text-gray-500 mb-4">Please try again or contact support.</p>
                <a href="/khargone.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Go Back</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    // Handle GET request (from khargone.php redirect)
    if(isset($_GET['order_id']) && !empty($_GET['order_id'])) {
        $order_id = trim($_GET['order_id']);
        
        // Store in session for backup
        $_SESSION['order_id'] = $order_id;
        
        // Auto-submit form to proceed to payment
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirecting to Payment...</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <meta http-equiv="refresh" content="2;url=request.php">
        </head>
        <body class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Redirecting to Payment</h2>
                <p class="text-gray-600">Please wait while we prepare your payment...</p>
                <p class="text-sm text-gray-500 mt-4">Order ID: <?php echo htmlspecialchars($order_id); ?></p>
                
                <form id="paymentForm" method="POST" action="request.php">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                </form>
                
                <script>
                    // Auto-submit after 1 second
                    setTimeout(function() {
                        document.getElementById('paymentForm').submit();
                    }, 1000);
                </script>
            </div>
        </body>
        </html>
        <?php
    } else {
        // No order_id found, redirect to khargone form
        header("Location: /khargone.php");
        exit;
    }
}