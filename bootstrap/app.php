<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Ponto central de bootstrap da aplicação (Laravel 11+).
 *
 * Aqui é onde a aplicação é "montada": define-se o caminho base do
 * projeto e como o roteamento, os middlewares e o tratamento de
 * exceções devem se comportar. Esse arquivo substitui os antigos
 * arquivos de configuração espalhados em app/Http/Kernel.php e
 * app/Exceptions/Handler.php das versões anteriores do Laravel.
 */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Registra o arquivo de rotas HTTP (web.php) usado por todo
        // o site (Home, Contato, Login, Cadastro, Arquivos etc.).
        web: __DIR__.'/../routes/web.php',
        // Registra os comandos Artisan customizados (console.php).
        commands: __DIR__.'/../routes/console.php',
        // Endpoint padrão de "health check" do Laravel, útil para
        // monitoramento (ex.: load balancers, uptime checks).
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware configuration belongs here as the application grows.
        // Ex.: middlewares globais, grupos de rota (web/api) ou aliases
        // customizados seriam registrados dentro desta closure.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Exception configuration belongs here as the application grows.
        // Ex.: personalizar como exceções específicas são reportadas
        // ou renderizadas (páginas de erro customizadas, logs etc.).
    })->create();
