<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Level;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentLevelRepository implements LevelRepository
{
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Level::query();

        if ($search !== null && $search !== '') {
            $escapedSearch = addcslashes($search, '\\%_');
            $pattern = "%{$escapedSearch}%";
            $escapeClause = DB::connection()->getDriverName() === 'mysql'
                ? "ESCAPE '\\\\'"
                : "ESCAPE '\\'";

            $query->where(function ($query) use ($escapeClause, $pattern): void {
                $query->whereRaw("name LIKE ? {$escapeClause}", [$pattern])
                    ->orWhereRaw("summary LIKE ? {$escapeClause}", [$pattern])
                    ->orWhereRaw("description LIKE ? {$escapeClause}", [$pattern]);
            });
        }

        return $query->orderBy('number')->paginate($perPage);
    }

    public function findByNumber(int $number): ?Level
    {
        return Level::query()->where('number', $number)->first();
    }

    public function create(array $attributes): Level
    {
        $createdBy = $attributes['created_by'] ?? null;
        unset($attributes['created_by']);

        $level = Level::query()->create($attributes);

        if ($createdBy !== null) {
            $level->created_by = $createdBy;
            $level->save();
        }

        return $level->refresh();
    }

    public function update(Level $level, array $attributes): Level
    {
        $level->fill($attributes);
        $level->save();

        return $level->refresh();
    }

    public function delete(Level $level): bool
    {
        return (bool) $level->delete();
    }

    public function nextNumber(): int
    {
        return ((int) Level::query()->max('number')) + 1;
    }
}
