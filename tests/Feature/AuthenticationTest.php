<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_a_hashed_password(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Maria da Silva',
            'username' => 'maria_silva',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertRedirect(route('home'));
        $user = User::query()->where('username', 'maria_silva')->firstOrFail();

        $this->assertNotSame('senha1234', $user->password);
        $this->assertTrue(Hash::check('senha1234', $user->password));
        $this->assertSame(Role::Member, $user->role);
    }

    public function test_registration_rejects_short_password_and_duplicate_username(): void
    {
        User::factory()->create(['username' => 'existing_user']);

        $this->from(route('home'))->post(route('register'), [
            'name' => 'Maria da Silva',
            'username' => 'new_user',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        $this->from(route('home'))->post(route('register'), [
            'name' => 'Maria da Silva',
            'username' => 'existing_user',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ])->assertSessionHasErrors('username');
    }

    public function test_registration_cannot_escalate_role(): void
    {
        $this->post(route('register'), [
            'name' => 'Maria da Silva',
            'username' => 'maria_admin',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
            'role' => 'admin',
        ]);

        $this->assertSame(Role::Member, User::query()->where('username', 'maria_admin')->firstOrFail()->role);
    }

    public function test_user_can_login_and_wrong_password_is_generic(): void
    {
        User::factory()->create(['username' => 'maria_silva', 'password' => 'senha1234']);

        $this->post(route('login'), [
            'username' => 'maria_silva',
            'password' => 'senha1234',
        ])->assertRedirect(route('home'));

        $this->assertTrue(Auth::check());
        $this->post(route('logout'));

        $this->from(route('home'))->post(route('login'), [
            'username' => 'maria_silva',
            'password' => 'senha_errada',
        ])->assertSessionHasErrors(['username' => 'Credenciais inválidas']);
        $this->assertFalse(Auth::check());
    }

    public function test_logout_ends_the_session_and_guests_are_redirected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();

        $this->get(route('files'))
            ->assertRedirect(route('home').'#login');
    }

    public function test_sixth_login_attempt_is_throttled(): void
    {
        User::factory()->create(['username' => 'maria_silva', 'password' => 'senha1234']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login'), [
                'username' => 'maria_silva',
                'password' => 'senha_errada',
            ]);
        }

        $this->post(route('login'), [
            'username' => 'maria_silva',
            'password' => 'senha_errada',
        ])->assertStatus(429);
    }
}
