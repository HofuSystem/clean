<?php

return [
    'ga4' => [
        'ios' => [
            'firebase_app_id' => env('GA4_FIREBASE_APP_ID_IOS'),
            'api_secret' => env('GA4_API_SECRET_IOS'),
        ],
        'android' => [
            'firebase_app_id' => env('GA4_FIREBASE_APP_ID_ANDROID'),
            'api_secret' => env('GA4_API_SECRET_ANDROID'),
        ],
    ],
    'branch' => [
        'ios_key' => env('BRANCH_KEY_IOS'),
        'android_key' => env('BRANCH_KEY_ANDROID'),
    ],
];
