<?php
/**
 * PayPal Setting & API Credentials
 * Created by Raza Mehdi <srmk@outlook.com>.
 */

 return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'), // 'sandbox' hoặc 'live'
    'sandbox' => [
        'client_id'     => env('PAYPAL_CLIENT_ID', ''), // Đổi thành PAYPAL_CLIENT_ID
        'client_secret' => env('PAYPAL_SECRET', ''), // Đổi thành PAYPAL_SECRET
    ],
    'live' => [
        'client_id'     => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
    ],
    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'),
    'currency'       => env('PAYPAL_CURRENCY', 'USD'),
];

