<?php

class PhonePeHelper
{
    private $apiUrl;
    private $env;
    private $clientId;
    private $clientSecret;
    private $clientVersion;

    public function __construct($clientId, $clientSecret, $clientVersion, $env)
    {
        if ($env === 'PRODUCTION') {
            $this->apiUrl = 'https://api.phonepe.com/apis';
        } else {
            $this->apiUrl = 'https://api-preprod.phonepe.com/apis/pg-sandbox';
        }
        $this->env = $env;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->clientVersion = $clientVersion;
    }

    public function getToken()
    {
        $url = $this->getUrl('token');

        $data = [
            "client_version" => $this->clientVersion,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            "grant_type"     => "client_credentials"
        ];
        $postFields = http_build_query($data);

        $headers = [
            "Content-Type: application/x-www-form-urlencoded"
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("cURL Error: " . curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($response, true);
        if (isset($result['access_token'])) {
            return $result['access_token'];
        }
        throw new Exception("Failed to get token: " . $response);
    }

    // More & Payload Info : https://developer.phonepe.com/payment-gateway/website-integration/standard-checkout/api-integration/api-reference/create-payment
    public function createPayment($orderId, $amount, $redirectUrl)
    {
        $url = $this->getUrl('pay');
        $token = $this->getToken();
        $payload = [
            "merchantOrderId" => $orderId,
            "amount"          => $amount,
            "paymentFlow"     => [
                "type"        => "PG_CHECKOUT",
                "message"     => "Payment message used for collect requests",
                "merchantUrls" => [
                    "redirectUrl" => $redirectUrl
                ]
            ]
        ];

        $headers = [
            "Content-Type: application/json",
            "Authorization: O-Bearer {$token}"
        ];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("cURL Error: " . curl_error($ch));
        }
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['redirectUrl'])) {
            return $result;
        }

        throw new Exception("Payment failed: " . $response);
    }

    // For More Info : https://developer.phonepe.com/payment-gateway/website-integration/standard-checkout/api-integration/api-reference/order-status
    // $details = fales, get the latest details of last attemp payment, else get all details of attemp of payment
    public function checkPaymentStatus($orderId, $details = false)
    {
        $token = $this->getToken();
        $url = $this->getUrl('status') . "?details=" . ($details ? 'true' : 'false');

        // ✅ CHANGE {orderId} TO {merchantOrderId}
        $url = str_replace('{merchantOrderId}', $orderId, $url);

        $headers = [
            "Content-Type: application/json",
            "Authorization: O-Bearer {$token}"
        ];

        // ✅ DEBUG URL
        error_log("🔗 CHECK STATUS URL: " . $url);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("cURL Error: " . curl_error($ch));
        }
        curl_close($ch);

        $result = json_decode($response, true);

        // ✅ BETTER RESPONSE CHECKING
        if (isset($result['orderId']) || isset($result['state'])) {
            return $result;
        }

        throw new Exception("Failed to get payment status: " . $response);
    }


    private function getUrl($type = null)
    {
        if ($type === 'token') {
            if ($this->env === 'PRODUCTION') {
                return $this->apiUrl . '/identity-manager/v1/oauth/token';
            } else {
                return $this->apiUrl . '/v1/oauth/token';
            }
        } elseif ($type === 'pay') {
            if ($this->env === 'PRODUCTION') {
                return $this->apiUrl . '/pg/checkout/v2/pay';
            } else {
                return $this->apiUrl . '/checkout/v2/pay';
            }
        } elseif ($type === 'status') {
            // ✅ CHANGE {orderId} TO {merchantOrderId}
            if ($this->env === 'PRODUCTION') {
                return $this->apiUrl . '/pg/checkout/v2/order/{merchantOrderId}/status';
            } else {
                return $this->apiUrl . '/checkout/v2/order/{merchantOrderId}/status';
            }
        }
        return null;
    }
}
