// config/services.php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Internal Service URLs
    | Semua komunikasi internal pakai base URL ini
    |--------------------------------------------------------------------------
    */

    'core_service' => [
        'base_url' => env('CORE_SERVICE_URL', 'http://localhost:8001'),
    ],

    'product_service' => [
        'base_url' => env('PRODUCT_SERVICE_URL', 'http://localhost:8002'),
    ],

    'order_service' => [
        'base_url' => env('ORDER_SERVICE_URL', 'http://localhost:8003'),
    ],

    'report_service' => [
        'base_url' => env('REPORT_SERVICE_URL', 'http://localhost:8004'),
    ],

    'internal_token' => env('INTERNAL_SERVICE_TOKEN'),

];