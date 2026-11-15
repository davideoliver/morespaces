<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Enums\Role;
use App\Models\User;

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

Artisan::command('morespaces:set-role {username} {role}', function (string $username, string $role): int {
    $targetRole = Role::tryFrom($role);

    if ($targetRole === null) {
        $this->error('Papel inválido. Use: member, agent ou admin.');

        return 1;
    }

    $user = User::query()->where('username', $username)->first();

    if ($user === null) {
        $this->error('Usuário não encontrado.');

        return 1;
    }

    $user->role = $targetRole;
    $user->save();
    $this->info('Papel atualizado com sucesso.');

    return 0;
})->purpose('Atualiza o papel de um usuário MoreSpaces');
