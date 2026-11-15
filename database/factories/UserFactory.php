<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function configure(): static
    {
        return $this->afterMaking(function (User $user): void {
            $user->role = Role::Member;
        });
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'password' => 'password',
            'role' => Role::Member,
        ];
    }

    public function member(): static
    {
        return $this->afterMaking(function (User $user): void {
            $user->role = Role::Member;
        });
    }

    public function agent(): static
    {
        return $this->afterMaking(function (User $user): void {
            $user->role = Role::Agent;
        });
    }

    public function admin(): static
    {
        return $this->afterMaking(function (User $user): void {
            $user->role = Role::Admin;
        });
    }
}
