<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class AddLogContext
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::withContext([
            'ip' => $request->ip(),
            'method' => $request->method(),
            'path' => $request->path(),
        ]);

        return $next($request);
    }
}
