<?php
// includes/config/payment-config.php
return [
    'phonepe' => [
        'mode' => 'sandbox', // 'sandbox' or 'production'
        
        'sandbox' => [
            'base_url' => 'https://api-preprod.phonepe.com/apis/pg-sandbox',
            'merchant_id' => 'PGTESTPAYUAT',
            'salt_key' => '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399',
            'salt_index' => 1
        ],
        
        'production' => [
            'base_url' => 'https://api.phonepe.com/apis/hermes',
            'merchant_id' => 'YOUR_LIVE_MERCHANT_ID',
            'salt_key' => 'YOUR_LIVE_SALT_KEY',
            'salt_index' => 1
        ]
    ],
    
    'registration_fee' => 5000, // ₹5,000
    'currency' => 'INR'
];
?>