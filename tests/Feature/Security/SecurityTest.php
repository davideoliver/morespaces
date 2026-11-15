<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Enums\DangerLevel;
use App\Enums\Role;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withSession(['_token' => csrf_token()]);
    }

    public function test_sql_injection_payloads_do_not_bypass_auth_or_damage_levels(): void
    {
        $this->post(route('login'), [
            'username' => "' OR '1'='1' --",
            'password' => 'qualquer-senha',
            '_token' => csrf_token(),
        ])->assertRedirect(route('home').'#login');
        $this->assertGuest();

        $agent = User::factory()->agent()->create();
        Level::factory()->create();
        $before = Level::query()->count();

        $response = $this->actingAs($agent)->getJson('/api/v1/levels?q='.urlencode('1; DROP TABLE levels;--'));

        $response->assertSuccessful();
        $this->assertSame($before, Level::query()->count());
        $this->assertDatabaseHas('levels', ['id' => Level::query()->firstOrFail()->id]);
    }

    public function test_search_escapes_reflected_xss_payload(): void
    {
        $agent = User::factory()->agent()->create();
        $payload = '<script>alert(1)</script>';

        $this->actingAs($agent)
            ->get('/arquivos?q='.urlencode($payload))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee($payload, false);
    }

    public function test_protected_endpoints_reject_guests_with_expected_responses(): void
    {
        $this->get(route('files'))->assertRedirect(route('home').'#login');
        $this->get(route('admin.messages'))->assertRedirect(route('home').'#login');
        $this->getJson('/api/v1/levels')->assertUnauthorized();
    }

    public function test_members_and_agents_cannot_perform_restricted_actions(): void
    {
        $member = User::factory()->member()->create();
        $level = Level::factory()->create();
        $payload = [
            '_token' => csrf_token(),
            'number' => '123',
            'name' => 'Nível protegido',
            'summary' => 'Resumo',
            'description' => 'Descrição protegida',
            'danger_level' => DangerLevel::Low->value,
        ];

        $this->actingAs($member)->get(route('files'))->assertForbidden();

        $agent = User::factory()->agent()->create();
        $this->actingAs($agent)->post(route('files.store'), $payload)->assertForbidden();
        $this->actingAs($agent)->put(route('files.update', $level), $payload)->assertForbidden();
        $this->actingAs($agent)
            ->withHeader('X-CSRF-TOKEN', csrf_token())
            ->delete(route('files.destroy', $level))
            ->assertForbidden();
    }

    public function test_replaying_a_session_cookie_after_logout_is_unauthenticated(): void
    {
        $user = User::factory()->agent()->create([
            'username' => 'cookie_user',
            'password' => 'senha1234',
        ]);

        $login = $this->post(route('login'), [
            'username' => $user->username,
            'password' => 'senha1234',
            '_token' => csrf_token(),
        ])->assertRedirect(route('home'));

        $cookie = collect($login->headers->getCookies())
            ->first(fn ($item): bool => $item->getName() === config('session.cookie'));
        $this->assertNotNull($cookie);

        $this->withHeader('X-CSRF-TOKEN', csrf_token())
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->withCookie($cookie->getName(), $cookie->getValue())
            ->get(route('files'))
            ->assertRedirect(route('home').'#login');
    }

    public function test_post_without_csrf_token_returns_419(): void
    {
        $this->app->instance(ValidateCsrfToken::class, new class(
            $this->app,
            $this->app['encrypter'],
        ) extends ValidateCsrfToken {
            protected function runningUnitTests(): bool
            {
                return false;
            }
        });

        $response = $this->postJson(route('contact'), [
            'nome' => 'Pessoa Teste',
            'contatoEmail' => 'teste@example.com',
            'mensagem' => 'Mensagem sem token CSRF',
        ]);
        $response->assertStatus(419);
    }

    public function test_registration_ignores_admin_role_payload(): void
    {
        $this->post(route('register'), [
            'name' => 'Pessoa Membro',
            'username' => 'payload_admin',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
            'role' => Role::Admin->value,
            '_token' => csrf_token(),
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'username' => 'payload_admin',
            'role' => Role::Member->value,
        ]);
    }
}
