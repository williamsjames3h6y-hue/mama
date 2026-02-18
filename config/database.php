<?php

return [
    'driver' => getenv('DB_DRIVER') ?: 'supabase',

    'mysql' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'earningsllc',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    'supabase' => [
        'url' => getenv('VITE_SUPABASE_URL'),
        'anon_key' => getenv('VITE_SUPABASE_SUPABASE_ANON_KEY'),
        'service_key' => getenv('SUPABASE_SERVICE_ROLE_KEY'),
    ],
];
