<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configure multiple payment gateways for global non-profit operations.
    | Each gateway has specific credentials and settings.
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'stripe'),

    'gateways' => [
        // Stripe (Global)
        'stripe' => [
            'driver' => 'stripe',
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'currency' => env('STRIPE_CURRENCY', 'USD'),
            'locale' => env('STRIPE_LOCALE', 'en'),
            'enabled' => env('STRIPE_ENABLED', true),
        ],

        // Razorpay (India)
        'razorpay' => [
            'driver' => 'razorpay',
            'key_id' => env('RAZORPAY_KEY_ID'),
            'key_secret' => env('RAZORPAY_KEY_SECRET'),
            'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
            'currency' => env('RAZORPAY_CURRENCY', 'INR'),
            'enabled' => env('RAZORPAY_ENABLED', false),
        ],

        // Cashfree (India)
        'cashfree' => [
            'driver' => 'cashfree',
            'app_id' => env('CASHFREE_APP_ID'),
            'secret_key' => env('CASHFREE_SECRET_KEY'),
            'client_id' => env('CASHFREE_CLIENT_ID'),
            'client_secret' => env('CASHFREE_CLIENT_SECRET'),
            'environment' => env('CASHFREE_ENV', 'test'), // test or production
            'currency' => env('CASHFREE_CURRENCY', 'INR'),
            'enabled' => env('CASHFREE_ENABLED', false),
        ],

        // Paytm (India)
        'paytm' => [
            'driver' => 'paytm',
            'merchant_id' => env('PAYTM_MERCHANT_ID'),
            'merchant_key' => env('PAYTM_MERCHANT_KEY'),
            'website' => env('PAYTM_WEBSITE', 'DEFAULT'),
            'channel' => env('PAYTM_CHANNEL', 'WEB'),
            'industry_type' => env('PAYTM_INDUSTRY_TYPE', 'Retail'),
            'environment' => env('PAYTM_ENV', 'staging'), // staging or production
            'callback_url' => env('PAYTM_CALLBACK_URL'),
            'currency' => env('PAYTM_CURRENCY', 'INR'),
            'enabled' => env('PAYTM_ENABLED', false),
        ],

        // PhonePe (India) - Uses direct API
        'phonepe' => [
            'driver' => 'phonepe',
            'merchant_id' => env('PHONEPE_MERCHANT_ID'),
            'salt_key' => env('PHONEPE_SALT_KEY'),
            'salt_index' => env('PHONEPE_SALT_INDEX', 1),
            'environment' => env('PHONEPE_ENV', 'test'), // test or production
            'callback_url' => env('PHONEPE_CALLBACK_URL'),
            'currency' => env('PHONEPE_CURRENCY', 'INR'),
            'enabled' => env('PHONEPE_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Payment Settings
    |--------------------------------------------------------------------------
    */
    'currency_symbol' => [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',
    ],

    'decimal_places' => 2,

    'supported_currencies' => ['USD', 'EUR', 'GBP', 'INR'],
];
