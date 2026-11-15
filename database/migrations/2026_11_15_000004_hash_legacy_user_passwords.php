<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->select(['id', 'password'])
            ->orderBy('id')
            ->get()
            ->each(function (object $user): void {
                if ($this->isBcryptHash((string) $user->password)) {
                    return;
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'password' => Hash::make((string) $user->password),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
    }

    private function isBcryptHash(string $password): bool
    {
        return preg_match('/^\$2[aby]\$\d{2}\$.{53}$/', $password) === 1;
    }
};
