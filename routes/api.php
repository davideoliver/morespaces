<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\LevelController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

/*
 * A autenticação desta API usa a sessão existente. O cliente deve primeiro
 * carregar a página de login, que define o cookie XSRF-TOKEN, e enviar seu
 * valor de volta no cabeçalho X-XSRF-TOKEN em toda requisição que altera dados.
 * O middleware web é intencional: sessão e proteção CSRF também se aplicam a
 * /api/*; essas rotas não são isentas de CSRF.
 */
Route::middleware(['web', 'auth', 'throttle:api', ValidateCsrfToken::class])->group(function (): void {
    Route::get('/v1/me', [LevelController::class, 'me'])->name('api.v1.me');

    Route::get('/v1/levels', [LevelController::class, 'index'])
        ->middleware('can:viewAny,App\Models\Level')
        ->name('api.v1.levels.index');
    Route::post('/v1/levels', [LevelController::class, 'store'])
        ->name('api.v1.levels.store');
    Route::get('/v1/levels/{level}', [LevelController::class, 'show'])
        ->whereNumber('level')
        ->middleware('can:view,level')
        ->name('api.v1.levels.show');
    Route::put('/v1/levels/{level}', [LevelController::class, 'update'])
        ->whereNumber('level')
        ->name('api.v1.levels.update');
    Route::patch('/v1/levels/{level}', [LevelController::class, 'update'])
        ->whereNumber('level')
        ->name('api.v1.levels.patch');
    Route::delete('/v1/levels/{level}', [LevelController::class, 'destroy'])
        ->whereNumber('level')
        ->name('api.v1.levels.destroy');
});
