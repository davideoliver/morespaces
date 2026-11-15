<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/**
 * Rotas HTTP públicas da aplicação MoreSpaces.
 *
 * Todas as rotas usam o método correspondente ao tipo de ação:
 * GET para exibição de páginas e POST para submissão de formulários
 * (contato, login, cadastro, logout). Cada rota é nomeada (->name())
 * para permitir referenciá-la nas views via route('nome') em vez de
 * escrever URLs "hardcoded".
 */

// Página inicial (landing page com Home, Sobre, FAQ, Contato, Login e Cadastro).
Route::get('/', [HomeController::class, 'index'])->name('home');

// Recebe o formulário de contato da seção #contact.
Route::post('/enviar', [ContactController::class, 'store'])
    ->middleware(['throttle:contact'])
    ->name('contact');

Route::get('/admin/mensagens', [ContactController::class, 'index'])
    ->middleware(['auth', 'can:viewAny,App\Models\ContactMessage'])
    ->name('admin.messages');

// Recebe o formulário de login da seção #login.
Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['guest', 'throttle:login'])
    ->name('login');

// Recebe o formulário de cadastro da seção #register.
Route::post('/cadastro', [AuthController::class, 'register'])
    ->middleware(['guest', 'throttle:register'])
    ->name('register');

// Encerra a sessão do usuário autenticado.
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth'])
    ->name('logout');

// Lista os níveis disponíveis na área restrita de Arquivos (M.G.E).
Route::middleware(['auth', 'role:agent,admin'])->group(function (): void {
    Route::get('/arquivos', [FilesController::class, 'index'])->name('files.index');
    Route::get('/arquivos/novo', [FilesController::class, 'create'])->name('files.create');
    Route::post('/arquivos', [FilesController::class, 'store'])->name('files.store');
    Route::get('/arquivos/{level}/editar', [FilesController::class, 'edit'])
        ->whereNumber('level')
        ->name('files.edit');
    Route::get('/arquivos/{level}', [FilesController::class, 'show'])
        ->whereNumber('level')
        ->name('files.show');
    Route::put('/arquivos/{level}', [FilesController::class, 'update'])
        ->whereNumber('level')
        ->name('files.update');
    Route::delete('/arquivos/{level}', [FilesController::class, 'destroy'])
        ->whereNumber('level')
        ->name('files.destroy');

});

// Alias legado sem duplicar a rota canônica usada pelo roteador.
Route::get('/arquivos/{legacy?}', [FilesController::class, 'index'])
    ->where('legacy', '__legacy__')
    ->middleware(['auth', 'role:agent,admin'])
    ->name('files');

Route::get('/arquivos/{level}/{legacy?}', [FilesController::class, 'show'])
    ->whereNumber('level')
    ->where('legacy', '__legacy__')
    ->middleware(['auth', 'role:agent,admin'])
    ->name('files.level');
