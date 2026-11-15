<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Enums\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_is_not_mass_assignable(): void
    {
        $user = User::create([
            'name' => 'Membro',
            'username' => 'member',
            'password' => 'password',
            'role' => Role::Admin,
        ]);

        $this->assertSame(Role::Member, $user->fresh()->role);
    }

    public function test_repository_can_update_role_explicitly(): void
    {
        $user = User::factory()->create();

        $updated = $this->app->make(UserRepository::class)->updateRole($user, Role::Agent);

        $this->assertSame(Role::Agent, $updated->role);
    }
}
