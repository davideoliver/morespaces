<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\DangerLevel;
use App\Logging\RequestContextProcessor;
use App\Models\User;
use App\Services\ContactService;
use App\Services\LevelService;
use DateTimeImmutable;
use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Monolog\LogRecord;
use RuntimeException;
use Tests\TestCase;

class ObservabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_is_logged_without_credentials(): void
    {
        Log::spy();
        $user = User::factory()->create(['username' => 'explorer', 'password' => 'senha-segura']);

        $this->post(route('login'), [
            'username' => 'explorer',
            'password' => 'senha-segura',
        ])->assertRedirect();

        Log::shouldHaveReceived('info')
            ->with('auth.login.success', \Mockery::on(
                fn (array $context): bool => $context['user_id'] === $user->id
                    && array_key_exists('ip', $context)
            ))
            ->once();
    }

    public function test_failed_login_logs_username_but_never_password(): void
    {
        Log::spy();
        $password = 'senha-que-nao-pode-ser-logada';

        $this->post(route('login'), [
            'username' => 'unknown_explorer',
            'password' => $password,
        ])->assertSessionHasErrors('username');

        Log::shouldHaveReceived('warning')
            ->with('auth.login.failed', \Mockery::on(
                fn (array $context): bool => $context['username'] === 'unknown_explorer'
                    && ! in_array($password, $context, true)
            ))
            ->once();
    }

    public function test_logout_is_logged(): void
    {
        Log::spy();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'));

        Log::shouldHaveReceived('info')
            ->with('auth.logout', \Mockery::on(
                fn (array $context): bool => $context['user_id'] === $user->id
            ))
            ->once();
    }

    public function test_level_and_contact_creation_are_logged_with_ids_only(): void
    {
        Log::spy();
        $user = User::factory()->admin()->create();

        $this->app->make(LevelService::class)->create([
            'number' => 80,
            'name' => 'Nível de teste',
            'description' => 'Descrição de teste.',
            'danger_level' => DangerLevel::Safe,
        ], $user);

        $this->app->make(ContactService::class)->create([
            'nome' => 'Visitante',
            'contatoEmail' => 'visitante@example.com',
            'mensagem' => 'Mensagem privada que não deve ser registrada.',
        ], $user);

        Log::shouldHaveReceived('info')->with('level.created', \Mockery::on(
            fn (array $context): bool => array_key_exists('level_id', $context)
                && $context['user_id'] === $user->id
                && count($context) === 2
        ))->once();
        Log::shouldHaveReceived('info')->with('contact.message.created', \Mockery::on(
            fn (array $context): bool => array_key_exists('message_id', $context)
                && $context['user_id'] === $user->id
                && count($context) === 2
        ))->once();
    }

    public function test_internal_errors_are_logged_with_safe_context(): void
    {
        Log::spy();
        $user = User::factory()->create();

        $this->actingAs($user)->get('/up');
        report(new RuntimeException('falha interna de teste'));

        Log::shouldHaveReceived('error')
            ->with('internal.error', \Mockery::on(
                fn (array $context): bool => $context['user_id'] === $user->id
                    && isset($context['url'], $context['exception'])
                    && $context['exception'] === RuntimeException::class
            ))
            ->once();
    }

    public function test_context_processor_redacts_sensitive_values_recursively(): void
    {
        $record = new LogRecord(
            new DateTimeImmutable,
            'testing',
            Level::Info,
            'request',
            [
                'password' => 'secret',
                'nested' => ['_token' => 'csrf-secret', 'cookie' => 'cookie-secret'],
                'safe' => 'retained',
            ],
        );

        $processed = $this->app->make(RequestContextProcessor::class)($record);

        $this->assertSame('[REDACTED]', $processed->context['password']);
        $this->assertSame('[REDACTED]', $processed->context['nested']['_token']);
        $this->assertSame('[REDACTED]', $processed->context['nested']['cookie']);
        $this->assertSame('retained', $processed->context['safe']);
        $this->assertStringNotContainsString('secret', json_encode($processed->context));
    }
}
