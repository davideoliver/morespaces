<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Role;
use App\Models\User;

class EloquentUserRepository implements UserRepository
{
    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }

    public function findByUsername(string $username): ?User
    {
        return User::query()->where('username', $username)->first();
    }

    public function updateRole(User $user, Role $role): User
    {
        $user->role = $role;
        $user->save();

        return $user->refresh();
    }
}
