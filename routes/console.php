<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/**
 * Arquivo de comandos Artisan "fechados" (closure-based commands).
 *
 * Diferente das rotas web, aqui não tratamos requisições HTTP — são
 * comandos executados via terminal (ex.: `php artisan inspire`).
 * Serve para tarefas administrativas, scripts de manutenção ou,
 * como neste caso, comandos de exemplo do próprio Laravel.
 */
Artisan::command('inspire', function (): void {
    // Comando de exemplo padrão do Laravel: apenas imprime uma
    // frase inspiradora aleatória no terminal.
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
