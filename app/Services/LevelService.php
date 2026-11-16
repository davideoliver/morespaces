<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\DuplicateLevelNumberException;
use App\Models\Level;
use App\Models\User;
use App\Repositories\LevelRepository;
use Illuminate\Support\Facades\Log;

class LevelService
{
    public function __construct(
        private readonly LevelRepository $levels,
    ) {
    }

    public function paginate(?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->levels->paginate(
            $this->sanitizeSearch($search),
            (int) config('morespaces.levels_per_page', 9),
        );
    }

    /** @param array<string, mixed> $data */
    public function create(array $data, User $user): Level
    {
        $number = $data['number'] ?? $this->levels->nextNumber();

        if ($this->levels->findByNumber((int) $number) !== null) {
            throw new DuplicateLevelNumberException;
        }

        $level = $this->levels->create($this->attributes($data, (int) $number, $user));

        Log::info('level.created', [
            'level_id' => $level->getKey(),
            'user_id' => $user->getAuthIdentifier(),
        ]);

        return $level;
    }

    /** @param array<string, mixed> $data */
    public function update(Level $level, array $data): Level
    {
        $number = array_key_exists('number', $data) && $data['number'] !== null
            ? (int) $data['number']
            : $level->number;

        $existing = $this->levels->findByNumber($number);
        if ($existing !== null && ! $existing->is($level)) {
            throw new DuplicateLevelNumberException;
        }

        return $this->levels->update($level, $this->attributes($data, $number));
    }

    public function delete(Level $level): bool
    {
        return $this->levels->delete($level);
    }

    /** @param array<string, mixed> $data */
    private function attributes(array $data, int $number, ?User $user = null): array
    {
        $attributes = [
            'number' => $number,
            'name' => $this->sanitize((string) $data['name']),
            'summary' => isset($data['summary']) ? $this->sanitize((string) $data['summary']) : null,
            'description' => $this->sanitize((string) $data['description']),
            'danger_level' => $data['danger_level'],
        ];

        if ($user !== null) {
            $attributes['created_by'] = $user->id;
        }

        return $attributes;
    }

    private function sanitize(string $value): string
    {
        $value = strip_tags($value);
        $value = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function sanitizeSearch(?string $search): ?string
    {
        if ($search === null) {
            return null;
        }

        return $this->sanitize($search);
    }
}
