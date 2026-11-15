<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactMessageRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): ContactMessage;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
