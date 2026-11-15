<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\Level;
use App\Models\User;

class LevelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::Agent, Role::Admin);
    }

    public function view(User $user, Level $level): bool
    {
        return $user->hasRole(Role::Agent, Role::Admin);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::Admin);
    }

    public function update(User $user, Level $level): bool
    {
        return $user->hasRole(Role::Admin);
    }

    public function delete(User $user, Level $level): bool
    {
        return $user->hasRole(Role::Admin);
    }
}
