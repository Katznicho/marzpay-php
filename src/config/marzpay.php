<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MarzPay API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the MarzPay API client.
    | You can set these values in your .env file or directly in this config.
    |
    */

    'api_key' => env('MARZPAY_API_KEY'),
    'api_secret' => env('MARZPAY_API_SECRET'),
    'base_url' => env('MARZPAY_BASE_URL', 'https://wallet.wearemarz.com/api/v1'),
    'timeout' => env('MARZPAY_TIMEOUT', 30),
];

