<?php

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
Route::post('/enviar', [HomeController::class, 'contact'])->name('contact');

// Recebe o formulário de login da seção #login.
Route::post('/login', [HomeController::class, 'login'])->name('login');

// Recebe o formulário de cadastro da seção #register.
Route::post('/cadastro', [HomeController::class, 'register'])->name('register');

// Encerra a sessão do usuário autenticado.
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');

// Lista os níveis disponíveis na área restrita de Arquivos (M.G.E).
Route::get('/arquivos', [HomeController::class, 'files'])->name('files');

// Exibe o conteúdo de um nível específico de arquivo.
// ->whereNumber('level') garante, via expressão regular da rota, que
// somente valores numéricos cheguem ao controller — qualquer outra
// coisa (ex.: /arquivos/abc) já resulta em 404 antes mesmo do controller.
Route::get('/arquivos/{level}', [HomeController::class, 'file'])->whereNumber('level')->name('files.level');
