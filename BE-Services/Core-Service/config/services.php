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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Internal service token — digunakan saat Core Service mengirim request
    // ke service lain (Product, Order, Report) via internal API call.
    // Nilai diambil dari .env — pastikan INTERNAL_SERVICE_TOKEN sama di semua service.
    'internal_token' => env('INTERNAL_SERVICE_TOKEN'),

    // URL masing-masing service — digunakan saat Core Service perlu
    // mengirim data snapshot atau memanggil endpoint internal service lain.
    // Contoh: setelah booking selesai, Core Service kirim snapshot ke Report Service.
    'product_service_url' => env('PRODUCT_SERVICE_URL', 'http://localhost:8001/api'),
    'order_service_url'   => env('ORDER_SERVICE_URL',   'http://localhost:8002/api'),
    'report_service_url'  => env('REPORT_SERVICE_URL',  'http://localhost:8003/api'),

];