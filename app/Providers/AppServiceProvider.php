<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\ContactMessageRepository;
use App\Repositories\EloquentContactMessageRepository;
use App\Repositories\EloquentLevelRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\LevelRepository;
use App\Repositories\UserRepository;
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
    }
}
