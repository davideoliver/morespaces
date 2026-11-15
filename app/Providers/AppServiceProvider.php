<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\ContactMessageRepository;
use App\Repositories\EloquentContactMessageRepository;
use App\Repositories\EloquentLevelRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\LevelRepository;
use App\Repositories\UserRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LevelRepository::class, EloquentLevelRepository::class);
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(ContactMessageRepository::class, EloquentContactMessageRepository::class);
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(
                Str::lower((string) $request->input('username')).'|'.$request->ip()
            );
        });

        RateLimiter::for('register', function (Request $request): Limit {
            return Limit::perMinute(5)->by(
                Str::lower((string) $request->input('username')).'|'.$request->ip()
            );
        });

        RateLimiter::for('contact', function (Request $request): Limit {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)->by(
                (string) ($request->user()?->getAuthIdentifier() ?? $request->ip())
            );
        });
    }
}
