<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('users')->upsert(
            [
                [
                    'name' => 'Agente de Demonstração',
                    'username' => 'demo_agent',
                    'password' => Hash::make('demo-agent-password'),
                    'role' => 'agent',
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Membro de Demonstração',
                    'username' => 'demo_member',
                    'password' => Hash::make('demo-member-password'),
                    'role' => 'member',
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['username'],
            ['name', 'password', 'role', 'updated_at']
        );
    }
}
