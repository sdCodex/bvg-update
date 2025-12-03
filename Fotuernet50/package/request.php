<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/PhonePeHelper.php';

if($_SERVER['REQUEST_METHOD']=='POST'){ 
    try {
        // ✅ FIX: Use the SAME order_id from preview.php
        if(isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
        } elseif(isset($_GET['order_id'])) {
            $order_id = $_GET['order_id'];
        } elseif(isset($_SESSION['unique_id'])) {
            $order_id = $_SESSION['unique_id'];
        } else {
            die("Order ID not found");
        }
        
        // ✅ FIX: CORRECT AMOUNT - ₹100 = 10000 paise
        $amount = 50000; // Fixed: ₹100 = 10000 paise
        
        $response_path = RESPONSE_PATH . "?order_id=" . $order_id;
        
        $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, ENV);
        $response = $phonePeHelper->createPayment($order_id, $amount, $response_path);
        
        echo "<script>location.href='".$response['redirectUrl']."';</script>";
        exit;   
    } catch (Throwable  $e) {
        echo "Payment Error: " . $e->getMessage();
        echo "<br><a href='../preview.php'>Go Back</a>";
        exit;
    }
} else {
    // ✅ Handle GET request from preview.php redirect
    if(isset($_GET['order_id'])) {
        // Auto-submit form to proceed to payment
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirecting to Payment...</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Redirecting to Payment</h2>
                <p class="text-gray-600">Please wait while we redirect you to PhonePe...</p>
                
                <form id="paymentForm" method="POST" action="request.php">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($_GET['order_id']); ?>">
                </form>
                
                <script>
                    setTimeout(function() {
                        document.getElementById('paymentForm').submit();
                    }, 1000);
                </script>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<script>location.href='../index.php';</script>";
    }
}
?>