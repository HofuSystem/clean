<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY', 'AIzaSyAj9Stxz6QIHwPbRbB6XRQQS6DjW7qGC3Q'),
    ],

    'myfatoorah' => [
        'api_key' => env('MYFATOORAH_API_TOKEN', 'SK_SAU_9EQIZJvQKmRlK5tDA8mWZLysvJ3upO6CoKh2HDE7Jo4347srddUCPZAMy4lvApSn'),
        'base_url' => env('MYFATOORAH_BASE_URL', 'https://api-sa.myfatoorah.com'),
        'success_url' => env('MYFATOORAH_SUCCESS_URL'),
        'error_url' => env('MYFATOORAH_ERROR_URL'),
        'country_code' => env('MYFATOORAH_COUNTRY_CODE', 'SAU'),

        'payment_method_ids' => [
            // Hardcoded payment method IDs to avoid API calls
            // Update these values based on your MyFatoorah account
            'apple_pay_id' => env('MYFATOORAH_APPLE_PAY_ID', 11),
            'mada_id' => env('MYFATOORAH_MADA_ID', 6),
            'google_pay_id' => env('MYFATOORAH_GOOGLE_PAY_ID', null),
        ],
    ],

];
