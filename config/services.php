<?php

return [

    // ... existing services

    'zoom' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'webhook_secret' => env('ZOOM_WEBHOOK_SECRET'),
    ],

    'wasabi' => [
        'key' => env('WASABI_ACCESS_KEY_ID'),
        'secret' => env('WASABI_SECRET_ACCESS_KEY'),
        'region' => env('WASABI_DEFAULT_REGION', 'us-east-1'),
        'bucket' => env('WASABI_BUCKET'),
        'endpoint' => env('WASABI_ENDPOINT', 'https://s3.wasabisys.com'),
    ],


    // --- Added from himmah1-main ---
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/login/google/callback'),
    ],

    // --- Added from project_structure ---
    // ... existing services

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'ai_limits' => [
        'free' => 50, // عدد الطلبات يومياً للمشترك المجاني
        'premium' => 500, // للمشترك المميز
    ],

    // --- Added from himmah1-main ---
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/login/google/callback'),
    ],

    // --- Added from himmah1-main ---
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/login/google/callback'),
    ],

    // --- Added from himmah1-main ---
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/login/google/callback'),
    ],

    // --- Added from project_structure ---
    // ... existing services

    'zoom' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'webhook_secret' => env('ZOOM_WEBHOOK_SECRET'),
    ],

    'wasabi' => [
        'key' => env('WASABI_ACCESS_KEY_ID'),
        'secret' => env('WASABI_SECRET_ACCESS_KEY'),
        'region' => env('WASABI_DEFAULT_REGION', 'us-east-1'),
        'bucket' => env('WASABI_BUCKET'),
        'endpoint' => env('WASABI_ENDPOINT', 'https://s3.wasabisys.com'),
    ],
];
