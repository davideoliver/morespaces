<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

final class LogAuthenticationEvents
{
    public function handle(Login|Failed|Logout $event): void
    {
        if ($event instanceof Login) {
            Log::info('auth.login.success', [
                'user_id' => $event->user->getAuthIdentifier(),
                'ip' => request()->ip(),
            ]);

            return;
        }

        if ($event instanceof Failed) {
            Log::warning('auth.login.failed', [
                'username' => (string) ($event->credentials['username'] ?? ''),
                'ip' => request()->ip(),
            ]);

            return;
        }

        Log::info('auth.logout', [
            'user_id' => $event->user?->getAuthIdentifier(),
            'ip' => request()->ip(),
        ]);
    }
}
