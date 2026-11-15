<?php

declare(strict_types=1);

return [
    'app_name' => env('APP_NAME', 'MoreSpaces'),
    'locale' => env('APP_LOCALE', 'pt_BR'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'session_secure_cookie' => (bool) env('SESSION_SECURE_COOKIE', false),
    'bcrypt_rounds' => (int) env('BCRYPT_ROUNDS', 12),
    'seed_admin_username' => env('SEED_ADMIN_USERNAME'),
    'seed_admin_password' => env('SEED_ADMIN_PASSWORD'),
    'levels_per_page' => (int) env('LEVELS_PER_PAGE', 9),
];
