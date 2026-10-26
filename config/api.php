<?php

return [
    'gateway' => env('APP_GATEWAY'),
    'username' => env('API_USERNAME'),
    'password' => env('API_PASSWORD'),
    'comp_id' => env('APP_URL'),
    'recaptcha' => [
        'enabled' => env('RECAPTCHA_ENABLED', false),
        'site_verify_url' => env('RECAPTCHA_SITE_VERIFY_URL'),
        'project_id' => env('RECAPTCHA_ENTERPRISE_PROJECT_ID'),
        'api_key' => env('RECAPTCHA_API_KEY'),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'allowed_score' => env('RECAPTCHA_ALLOWED_SCORE', 0.5),
    ],
];
