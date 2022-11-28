<?php

return [
    'timeout' => 120,
    'cache' => [
        'remember_for' => 30,
    ],
    'license' => [
        'api_id' => env('EMSZMAL_API_ID'),
        'api_key' => env('EMSZMAL_API_KEY'),
    ],
    'bank_credentials' => [
        'default' => [
            'provider' => env('EMSZMAL_BANK_PROVIDER_ID'),
            'login' => env('EMSZMAL_BANK_LOGIN'),
            'password' => env('EMSZMAL_BANK_PASSWORD'),
            'user_context' => env('EMSZMAL_BANK_USER_CONTEXT', 'I'),
            'token_value' => env('EMSZMAL_BANK_USER_TOKEN', '')
        ],
    ],
];
