<?php

return [

    /*
    |--------------------------------------------------------------------------
    | M-Pesa Environment
    |--------------------------------------------------------------------------
    |
    | Set to 'sandbox' for development/testing or 'production' for live.
    | Controls which Safaricom Daraja API base URL is used.
    |
    */
    'env' => env('MPESA_ENV', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Daraja API Credentials
    |--------------------------------------------------------------------------
    |
    | Consumer key and secret issued by Safaricom when you create an app
    | on the Daraja developer portal (developer.safaricom.co.ke).
    |
    */
    'consumer_key'    => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Business Shortcode
    |--------------------------------------------------------------------------
    |
    | Your M-Pesa Paybill or Till number. Used as the initiator for STK Push.
    |
    */
    'shortcode' => env('MPESA_SHORTCODE', ''),

    /*
    |--------------------------------------------------------------------------
    | Lipa Na M-Pesa Online Passkey
    |--------------------------------------------------------------------------
    |
    | Passkey provided by Safaricom for the STK Push (Lipa Na M-Pesa Online)
    | API. Used to generate the Password for STK Push requests.
    |
    */
    'passkey' => env('MPESA_PASSKEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Callback URL
    |--------------------------------------------------------------------------
    |
    | The publicly accessible URL where Safaricom will POST payment results.
    | Must be HTTPS in production. Use ngrok or similar for local development.
    |
    | Example: https://app.propertysasa.com/api/mpesa/callback
    |
    */
    'callback_url' => env('MPESA_CALLBACK_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | API Base URLs
    |--------------------------------------------------------------------------
    |
    | These are resolved automatically based on the 'env' value above.
    | Override only if Safaricom changes their API endpoint structure.
    |
    */
    'base_url' => [
        'sandbox'    => 'https://sandbox.safaricom.co.ke',
        'production' => 'https://api.safaricom.co.ke',
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Cache Duration
    |--------------------------------------------------------------------------
    |
    | Safaricom access tokens are valid for 3600 seconds (1 hour).
    | We cache them for slightly less to avoid edge-case expiry.
    |
    */
    'token_cache_seconds' => 3500,

];
