<?php

return [
    'admin' => [
        // Nilai default bisa dioverride via ENV jika diinginkan
        'name' => env('STARTER_ADMIN_NAME', 'Administrator'),
        'email' => env('STARTER_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('STARTER_ADMIN_PASSWORD', 'password'),
    ],
];