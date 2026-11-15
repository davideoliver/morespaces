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
    'events' => [
        ['year' => '1900', 'description' => 'Fundação da MoreSpaces'],
        ['year' => 'Hoje', 'description' => 'Expansão para novos espaços e colaboradores'],
    ],
    'faqs' => [
        ['question' => 'O que a companhia faz?', 'answer' => 'Ela atua na criação e distribuição de espaços, aplicando tecnologias para atender às necessidades de clientes e parceiros.'],
        ['question' => 'Onde nós estamos?', 'answer' => 'Em todos os espaços onde ideias precisam de estrutura para crescer.'],
        ['question' => 'Como posso colaborar?', 'answer' => 'Entre em contato pelo formulário ou conheça mais sobre nossos projetos.'],
    ],
];
