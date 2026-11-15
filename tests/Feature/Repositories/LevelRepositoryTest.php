<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Enums\DangerLevel;
use App\Models\Level;
use App\Repositories\LevelRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LevelRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_escapes_like_wildcards(): void
    {
        $repository = $this->app->make(LevelRepository::class);
        $repository->create([
            'number' => 1,
            'name' => 'Level 100% complete',
            'description' => 'Exact wildcard text.',
            'danger_level' => DangerLevel::Safe,
        ]);
        $repository->create([
            'number' => 2,
            'name' => 'Level 1000 complete',
            'description' => 'Should not match the escaped wildcard.',
            'danger_level' => DangerLevel::Safe,
        ]);

        $results = $repository->paginate('100%');

        $this->assertSame(1, $results->total());
        $this->assertSame('Level 100% complete', $results->first()->name);
    }

    public function test_find_by_number_returns_a_level_or_null(): void
    {
        $level = Level::factory()->create(['number' => 42]);
        $repository = $this->app->make(LevelRepository::class);

        $this->assertTrue($repository->findByNumber(42)->is($level));
        $this->assertNull($repository->findByNumber(404));
    }
}
