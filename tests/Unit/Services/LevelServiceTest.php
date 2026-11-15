<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\DangerLevel;
use App\Models\Level;
use App\Models\User;
use App\Repositories\LevelRepository;
use App\Services\LevelService;
use Mockery;
use Tests\TestCase;

class LevelServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_sanitizes_attributes_before_persisting(): void
    {
        $repository = Mockery::mock(LevelRepository::class);
        $user = new User;
        $user->id = 7;
        $stored = new Level(['number' => 8]);

        $repository->shouldReceive('findByNumber')->once()->with(8)->andReturnNull();
        $repository->shouldReceive('create')->once()->with(Mockery::on(
            static fn (array $attributes): bool => $attributes['name'] === 'Nome seguro'
                && $attributes['summary'] === 'Resumo limpo'
                && $attributes['description'] === 'Descrição limpa'
                && $attributes['danger_level'] === DangerLevel::Safe
                && $attributes['created_by'] === 7
        ))->andReturn($stored);

        $level = (new LevelService($repository))->create([
            'number' => 8,
            'name' => " <b>Nome</b> \n seguro ",
            'summary' => "Resumo\tlimpo",
            'description' => "Descrição\r\nlimpa",
            'danger_level' => DangerLevel::Safe,
        ], $user);

        $this->assertSame($stored, $level);
    }
}
