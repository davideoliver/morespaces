<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Role;
use App\Models\User;

interface UserRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): User;

    public function findByUsername(string $username): ?User;

    public function updateRole(User $user, Role $role): User;
}
