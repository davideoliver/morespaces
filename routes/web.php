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
Route::post('/enviar', [ContactController::class, 'store'])->name('contact');

// Recebe o formulário de login da seção #login.
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Recebe o formulário de cadastro da seção #register.
Route::post('/cadastro', [AuthController::class, 'register'])->name('register');

// Encerra a sessão do usuário autenticado.
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Lista os níveis disponíveis na área restrita de Arquivos (M.G.E).
Route::get('/arquivos', [FilesController::class, 'index'])->name('files');

// Exibe o conteúdo de um nível específico de arquivo.
// ->whereNumber('level') garante, via expressão regular da rota, que
// somente valores numéricos cheguem ao controller — qualquer outra
// coisa (ex.: /arquivos/abc) já resulta em 404 antes mesmo do controller.
Route::get('/arquivos/{level}', [FilesController::class, 'show'])->whereNumber('level')->name('files.level');
