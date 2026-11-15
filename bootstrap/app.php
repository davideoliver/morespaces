<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\AddLogContext;
use App\Http\Middleware\SecurityHeaders;
use App\Exceptions\DuplicateLevelNumberException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Ponto central de bootstrap da aplicação (Laravel 11+).
 *
 * Aqui é onde a aplicação é "montada": define-se o caminho base do
 * projeto e como o roteamento, os middlewares e o tratamento de
 * exceções devem se comportar. Esse arquivo substitui os antigos
 * arquivos de configuração espalhados em app/Http/Kernel.php e
 * app/Exceptions/Handler.php das versões anteriores do Laravel.
 */
return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\AppServiceProvider::class,
    ])
    ->withRouting(
        // Registra o arquivo de rotas HTTP (web.php) usado por todo
        // o site (Home, Contato, Login, Cadastro, Arquivos etc.).
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        // Registra os comandos Artisan customizados (console.php).
        commands: __DIR__.'/../routes/console.php',
        // Endpoint padrão de "health check" do Laravel, útil para
        // monitoramento (ex.: load balancers, uptime checks).
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->append(AddLogContext::class);
        $middleware->alias([
            'role' => EnsureRole::class,
        ]);
        $middleware->redirectGuestsTo(
            fn (Request $request): string => route('home').'#login'
        );
        $middleware->redirectUsersTo(
            fn (Request $request): string => route('home')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $exception): void {
            $request = request();

            if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
                return;
            }

            Log::error('internal.error', [
                'user_id' => $request->user()?->getAuthIdentifier(),
                'url' => $request->fullUrl(),
                'exception' => $exception::class,
            ]);
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            $isJsonRequest = $request->is('api/*') || $request->expectsJson();

            if ($exception instanceof ValidationException && ! $isJsonRequest) {
                $section = match (true) {
                    $request->routeIs('register') => 'register',
                    $request->routeIs('contact') => 'contact',
                    default => 'login',
                };

                return redirect()->to(route('home').'#'.$section)
                    ->withErrors($exception->errors())
                    ->withInput();
            }

            if ($exception instanceof AuthenticationException && ! $isJsonRequest) {
                return redirect()->to(route('home').'#login');
            }

            if ($exception instanceof ValidationException) {
                $status = 422;
                $error = 'validation_error';
                $message = 'Os dados enviados são inválidos.';
                $errors = $exception->errors();
            } elseif ($exception instanceof AuthenticationException) {
                $status = 401;
                $error = 'unauthenticated';
                $message = 'É necessário estar autenticado para continuar.';
                $errors = null;
            } elseif (
                $exception instanceof AuthorizationException
                || $exception instanceof AccessDeniedHttpException
                || ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() === 403)
            ) {
                $status = 403;
                $error = 'forbidden';
                $message = 'Você não tem permissão para acessar este recurso.';
                $errors = null;
            } elseif (
                $exception instanceof ModelNotFoundException
                || $exception instanceof NotFoundHttpException
                || ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() === 404)
            ) {
                $status = 404;
                $error = 'not_found';
                $message = 'O recurso solicitado não foi encontrado.';
                $errors = null;
            } elseif (
                $exception instanceof MethodNotAllowedHttpException
                || ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() === 405)
            ) {
                $status = 405;
                $error = 'method_not_allowed';
                $message = 'O método HTTP utilizado não é permitido neste recurso.';
                $errors = null;
            } elseif (
                $exception instanceof TokenMismatchException
                || ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() === 419)
            ) {
                $status = 419;
                $error = 'csrf_token_mismatch';
                $message = 'A sessão expirou. Atualize a página e tente novamente.';
                $errors = null;
            } elseif ($exception instanceof ThrottleRequestsException) {
                $status = 429;
                $error = 'too_many_requests';
                $message = 'Muitas tentativas. Aguarde um momento e tente novamente.';
                $errors = null;
            } elseif ($exception instanceof DuplicateLevelNumberException) {
                $status = 409;
                $error = 'duplicate_level_number';
                $message = $exception->getMessage();
                $errors = ['number' => [$message]];
            } else {
                $status = 500;
                $error = 'internal_server_error';
                $message = 'Ocorreu um erro interno. Tente novamente mais tarde.';
                $errors = null;
            }

            if ($isJsonRequest) {
                $payload = [
                    'status' => $status,
                    'error' => $error,
                    'message' => $message,
                ];

                if ($status === 422) {
                    $payload['errors'] = $errors;
                }

                if (config('app.debug')) {
                    $payload['debug'] = [
                        'exception' => $exception::class,
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                    ];
                }

                return response()->json($payload, $status);
            }

            if ($exception instanceof DuplicateLevelNumberException) {
                return back()->withErrors($errors)->withInput();
            }

            $view = in_array($status, [403, 404, 419, 429, 500], true)
                ? "errors.{$status}"
                : 'errors.500';

            return response()->view($view, [
                'exception' => $exception,
                'status' => $status,
                'message' => $message,
            ], $status);
        });
    })->create();
