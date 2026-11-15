<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Level;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LevelRepository
{
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator;

    public function findByNumber(int $number): ?Level;

    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): Level;

    /** @param array<string, mixed> $attributes */
    public function update(Level $level, array $attributes): Level;

    public function delete(Level $level): bool;

    public function nextNumber(): int;
}
