<?php
session_start();
include 'db_config.php';

// PhonePe Configuration
$merchantId = "SU2511151101041296360807";
$saltKey    = "f8ac834a-90ac-4596-ba60-dfa8b3cd35d9";
$saltIndex  = "1";

function verifyChecksum($response, $saltKey, $saltIndex) {
    if(isset($response['response']) && isset($response['checksum'])) {
        $base64Payload = $response['response'];
        $checksum = $response['checksum'];
        
        $expectedChecksum = hash('sha256', $base64Payload . $saltKey) . '###' . $saltIndex;
        
        return $checksum === $expectedChecksum;
    }
    return false;
}

// Handle PhonePe callback (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = $_POST;
    
    // Verify checksum
    if (verifyChecksum($response, $saltKey, $saltIndex)) {
        // Decode the response payload
        $decodedPayload = base64_decode($response['response']);
        $responseData = json_decode($decodedPayload, true);
        
        $transactionId = $responseData['data']['transactionId'];
        $merchantTransactionId = $responseData['data']['merchantTransactionId'];
        $amount = $responseData['data']['amount'];
        $state = $responseData['data']['state'];
        
        if ($state === 'COMPLETED') {
            // Payment successful - Update database
            $update_stmt = $conn->prepare("UPDATE fotuernet50_students SET 
                payment_status = 'SUCCESS', 
                phonepe_transaction_id = ?,
                amount = ?,
                payment_method = 'PhonePe'
                WHERE phonepe_transaction_id = ?");
            
            $update_stmt->bind_param("sis", 
                $transactionId, 
                $amount, 
                $merchantTransactionId
            );
            
            if($update_stmt->execute()) {
                // Success message
                $_SESSION['payment_success'] = true;
                $_SESSION['transaction_id'] = $transactionId;
                $_SESSION['amount'] = $amount / 100;
                
                // Redirect to avoid form resubmission
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                error_log("Database update failed for transaction: " . $merchantTransactionId);
            }
        } else {
            // Payment failed
            $update_stmt = $conn->prepare("UPDATE fotuernet50_students SET 
                payment_status = 'FAILED' 
                WHERE phonepe_transaction_id = ?");
            
            $update_stmt->bind_param("s", $merchantTransactionId);
            $update_stmt->execute();
            
            $_SESSION['payment_success'] = false;
            $_SESSION['payment_message'] = "Payment failed. Status: " . $state;
        }
    } else {
        $_SESSION['payment_success'] = false;
        $_SESSION['payment_message'] = "Checksum verification failed!";
    }
}

// Display success/failure message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if(isset($_SESSION['payment_success']) && $_SESSION['payment_success']): ?>
                    <div class="alert alert-success text-center">
                        <h4>✅ Payment Successful!</h4>
                        <p>Thank you for your payment. Your transaction has been completed successfully.</p>
                        <p><strong>Transaction ID:</strong> <?php echo $_SESSION['transaction_id']; ?></p>
                        <p><strong>Amount Paid:</strong> ₹<?php echo $_SESSION['amount']; ?></p>
                        <a href="student_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    </div>
                <?php elseif(isset($_SESSION['payment_success']) && !$_SESSION['payment_success']): ?>
                    <div class="alert alert-danger text-center">
                        <h4>❌ Payment Failed!</h4>
                        <p><?php echo $_SESSION['payment_message']; ?></p>
                        <a href="initiate_payment.php?unique_id=<?php echo $_SESSION['current_transaction']['unique_id'] ?? ''; ?>" class="btn btn-warning">Try Again</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <h4>Processing Payment...</h4>
                        <p>Please wait while we process your payment.</p>
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>';
                        }, 3000);
                    </script>
                <?php endif; ?>
                
                <?php
                // Clear session messages after displaying
                unset($_SESSION['payment_success']);
                unset($_SESSION['transaction_id']);
                unset($_SESSION['amount']);
                unset($_SESSION['payment_message']);
                ?>
            </div>
        </div>
    </div>
</body>
</html>