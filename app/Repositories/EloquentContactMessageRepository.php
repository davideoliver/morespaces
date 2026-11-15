<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentContactMessageRepository implements ContactMessageRepository
{
    public function create(array $attributes): ContactMessage
    {
        return ContactMessage::query()->create($attributes);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ContactMessage::query()
            ->latest('created_at')
            ->paginate($perPage);
    }
}
