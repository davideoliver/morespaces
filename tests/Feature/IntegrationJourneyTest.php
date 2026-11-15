<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_login_promote_create_search_logout_and_deny_access(): void
    {
        $this->post(route('register'), [
            'name' => 'Explorador integrado',
            'username' => 'journey_user',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ])->assertRedirect(route('home'));

        $this->assertGuest();

        User::query()->where('username', 'journey_user')->update(['role' => Role::Admin->value]);

        $this->post(route('login'), [
            'username' => 'journey_user',
            'password' => 'senha1234',
        ])->assertRedirect(route('home'));

        $this->post(route('files.store'), [
            'number' => 321,
            'name' => 'Nível da jornada',
            'summary' => 'Criado no fluxo integrado.',
            'description' => 'Descrição do nível criado durante a jornada.',
            'danger_level' => 'low',
        ])->assertRedirect();

        $this->get(route('files.index', ['q' => 'jornada']))
            ->assertOk()
            ->assertSee('Nível da jornada');

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
        $this->get(route('files.index'))->assertRedirect(route('home').'#login');
        $this->assertDatabaseHas('levels', ['number' => 321]);
    }
}
