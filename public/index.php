<?php

use Illuminate\Http\Request;

// Marca o instante exato em que o Laravel começou a processar a
// requisição. Usado internamente pelo framework para métricas de
// desempenho (ex.: barra de depuração / profiling).
define('LARAVEL_START', microtime(true));

// Carrega o autoloader do Composer, responsável por resolver
// automaticamente todas as classes/namespaces do projeto e das
// dependências instaladas via `composer install`.
require __DIR__.'/../vendor/autoload.php';

// Inicializa o container da aplicação (define os bindings, service
// providers, rotas etc. configurados em bootstrap/app.php).
$app = require_once __DIR__.'/../bootstrap/app.php';

// Ponto de entrada único (front controller) de toda a aplicação:
// captura a requisição HTTP atual, deixa o Laravel decidir qual rota
// e controller devem tratá-la, e envia a resposta de volta ao
// navegador. Todo o tráfego do site passa por este arquivo.
$app->handleRequest(Request::capture());
