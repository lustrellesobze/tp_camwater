<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://admin.camwater.cm',
        'https://app.camwater.cm',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Authorization',
        'Content-Type',
        'Content-Disposition',
        'X-CSRF-TOKEN'
    ],

    'max_age' => 0,

    'supports_credentials' => true,
];