<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::Admin);
    }

    public function delete(User $user, ContactMessage $contactMessage): bool
    {
        return $user->hasRole(Role::Admin);
    }
}
