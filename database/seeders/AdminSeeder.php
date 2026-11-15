<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = config('morespaces.seed_admin_username');
        $password = config('morespaces.seed_admin_password');

        if (! is_string($username) || $username === '' || ! is_string($password) || $password === '') {
            throw new RuntimeException(
                'SEED_ADMIN_USERNAME and SEED_ADMIN_PASSWORD must be configured before seeding.'
            );
        }

        DB::table('users')->updateOrInsert(
            ['username' => $username],
            [
                'name' => 'Administrador',
                'password' => Hash::make($password),
                'role' => 'admin',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
