<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/enviar', [HomeController::class, 'contact'])->name('contact');
Route::post('/login', [HomeController::class, 'login'])->name('login');
Route::post('/cadastro', [HomeController::class, 'register'])->name('register');