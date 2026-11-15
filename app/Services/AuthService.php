<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

        Log::info('Usuário criado', ['user_id' => $user->id, 'username' => $user->username]);

        return $user;
    }

    public function attempt(string $username, string $password): bool
    {
        $authenticated = Auth::attempt([
            'username' => trim($username),
            'password' => $password,
        ]);

        if ($authenticated) {
            Log::info('Login realizado', ['user_id' => Auth::id(), 'username' => trim($username)]);
        } else {
            Log::warning('Falha no login', ['username' => trim($username)]);
        }

        return $authenticated;
    }
}
