<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Decision Tree API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk koneksi ke Decision Tree API
    |
    */

    'api_url' => env('DECISION_TREE_API_URL', 'http://localhost:8001'),

    'timeout' => env('DECISION_TREE_API_TIMEOUT', 30),

    'retry_attempts' => env('DECISION_TREE_API_RETRY_ATTEMPTS', 3),
];

