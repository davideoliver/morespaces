<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $users,
    ) {
    }

    /** @param array{name: string, username: string, password: string} $data */
    public function register(array $data): User
    {
        $user = $this->users->create([
            'name' => trim($data['name']),
            'username' => trim($data['username']),
            'password' => Hash::make($data['password']),
        ]);
        $user->role = Role::Member;
        $user->save();

        return $user;
    }

    public function attempt(string $username, string $password): bool
    {
        try {
            $authenticated = Auth::attempt([
                'username' => trim($username),
                'password' => $password,
            ]);
        } catch (RuntimeException $exception) {
            if ($exception->getMessage() !== 'This password does not use the Bcrypt algorithm.') {
                throw $exception;
            }

            return false;
        }

        return $authenticated;
    }
}
