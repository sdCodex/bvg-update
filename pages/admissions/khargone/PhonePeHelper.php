<?php
// PhonePeHelper.php

class PhonePeHelper {
    private $apiUrl;
    private $env;
    private $clientId;
    private $clientSecret;
    private $clientVersion;
    private $merchantId; // Merchant ID is often required

    public function __construct($clientId, $clientSecret, $clientVersion, $env, $merchantId = null) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->clientVersion = $clientVersion;
        $this->env = strtoupper($env);
        $this->merchantId = $merchantId ?: $clientId; // Default to clientId if merchantId not provided
        
        // Set API URL based on environment
        if ($this->env === 'PRODUCTION') {
            $this->apiUrl = 'https://api.phonepe.com/apis/hermes';
        } else {
            $this->apiUrl = 'https://api-preprod.phonepe.com/apis/hermes/pg-sandbox';
        }
    }

    /**
     * Get OAuth token from PhonePe
     * @return string Access token
     * @throws Exception
     */
    public function getToken() {
        $url = $this->apiUrl . '/v1/oauth/token';
        
        $postData = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
            'client_version' => $this->clientVersion
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200) {
            $errorMsg = isset($result['message']) ? $result['message'] : 'Unknown error';
            throw new Exception("Token API Error ($httpCode): " . $errorMsg);
        }
        
        if (!isset($result['access_token'])) {
            throw new Exception("Invalid token response: " . $response);
        }
        
        return $result['access_token'];
    }

    /**
     * Create payment request
     * @param string $orderId Merchant order ID
     * @param int $amount Amount in paise
     * @param string $redirectUrl Callback URL
     * @param array $additionalData Additional payment data
     * @return array Payment response
     * @throws Exception
     */
    public function createPayment($orderId, $amount, $redirectUrl, $additionalData = []) {
        $token = $this->getToken();
        $url = $this->apiUrl . '/pg/v1/pay';
        
        // Prepare payload
        $payload = array_merge([
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $orderId,
            'amount' => $amount,
            'redirectUrl' => $redirectUrl,
            'redirectMode' => 'POST', // or 'GET' based on your requirement
            'paymentInstrument' => [
                'type' => 'PAY_PAGE'
            ]
        ], $additionalData);
        
        // Add customer info if available
        if (isset($additionalData['customer'])) {
            $payload['customer'] = $additionalData['customer'];
        }
        
        // Add merchant order ID if not already in payload
        if (!isset($payload['merchantOrderId'])) {
            $payload['merchantOrderId'] = $orderId;
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
                'X-VERIFY: ' . $this->generateSignature($payload)
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200) {
            $errorMsg = isset($result['message']) ? $result['message'] : 'Unknown error';
            throw new Exception("Payment API Error ($httpCode): " . $errorMsg);
        }
        
        // Check for success response
        if (isset($result['success']) && $result['success'] === true) {
            if (isset($result['data']['redirectUrl'])) {
                return [
                    'success' => true,
                    'redirectUrl' => $result['data']['redirectUrl'],
                    'transactionId' => $result['data']['transactionId'] ?? $orderId,
                    'merchantTransactionId' => $result['data']['merchantTransactionId'] ?? $orderId,
                    'response' => $result
                ];
            }
        }
        
        throw new Exception("Payment creation failed: " . json_encode($result));
    }

    /**
     * Check payment status
     * @param string $orderId Merchant order ID
     * @param bool $details Get detailed status
     * @return array Payment status
     * @throws Exception
     */
    public function checkPaymentStatus($orderId, $details = false) {
        $token = $this->getToken();
        
        // Construct status URL
        $url = $this->apiUrl . '/pg/v1/status/' . $this->merchantId . '/' . $orderId;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
                'X-MERCHANT-ID: ' . $this->merchantId
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200) {
            $errorMsg = isset($result['message']) ? $result['message'] : 'Unknown error';
            throw new Exception("Status API Error ($httpCode): " . $errorMsg);
        }
        
        return $result;
    }

    /**
     * Generate X-VERIFY signature
     * @param array $payload Request payload
     * @return string Signature
     */
    private function generateSignature($payload) {
        $payloadString = json_encode($payload);
        $base64Payload = base64_encode($payloadString);
        $signature = hash_hmac('sha256', $base64Payload, $this->clientSecret);
        return $signature . '###1'; // Version 1
    }

    /**
     * Validate callback/redirect signature
     * @param array $response Response data
     * @return bool Is signature valid
     */
    public function validateSignature($response) {
        if (!isset($response['response'])) {
            return false;
        }
        
        $responseData = $response['response'];
        $receivedSignature = $response['x_verify'] ?? '';
        
        // Remove version from signature
        $signatureParts = explode('###', $receivedSignature);
        if (count($signatureParts) !== 2) {
            return false;
        }
        
        $receivedHash = $signatureParts[0];
        $version = $signatureParts[1];
        
        // Generate our own signature
        $responseString = json_encode($responseData);
        $base64Response = base64_encode($responseString);
        $calculatedHash = hash_hmac('sha256', $base64Response, $this->clientSecret);
        
        return hash_equals($calculatedHash, $receivedHash);
    }

    /**
     * Refund payment
     * @param string $originalTransactionId Original transaction ID
     * @param string $refundId Refund ID
     * @param int $amount Amount to refund (in paise)
     * @param string $reason Refund reason
     * @return array Refund response
     * @throws Exception
     */
    public function initiateRefund($originalTransactionId, $refundId, $amount, $reason = '') {
        $token = $this->getToken();
        $url = $this->apiUrl . '/pg/v1/refund';
        
        $payload = [
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $refundId,
            'originalTransactionId' => $originalTransactionId,
            'amount' => $amount,
            'callbackUrl' => $this->getRefundCallbackUrl(),
            'refundReason' => $reason
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
                'X-VERIFY: ' . $this->generateSignature($payload)
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200) {
            $errorMsg = isset($result['message']) ? $result['message'] : 'Unknown error';
            throw new Exception("Refund API Error ($httpCode): " . $errorMsg);
        }
        
        return $result;
    }

    /**
     * Get refund callback URL
     * @return string
     */
    private function getRefundCallbackUrl() {
        // Define your refund callback URL
        return ($_SERVER['HTTPS'] ? 'https://' : 'http://') . 
               $_SERVER['HTTP_HOST'] . 
               '/payment-refund-callback.php';
    }

    /**
     * Log payment activities
     * @param string $message Log message
     * @param array $data Additional data
     */
    private function log($message, $data = []) {
        $logFile = __DIR__ . '/logs/phonepe_' . date('Y-m-d') . '.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logEntry = date('Y-m-d H:i:s') . " - " . $message;
        if (!empty($data)) {
            $logEntry .= " - " . json_encode($data);
        }
        $logEntry .= PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}