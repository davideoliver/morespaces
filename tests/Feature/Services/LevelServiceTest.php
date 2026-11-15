<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Enums\DangerLevel;
use App\Exceptions\DuplicateLevelNumberException;
use App\Models\Level;
use App\Models\User;
use App\Services\LevelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LevelServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_assigns_the_next_number_when_number_is_omitted(): void
    {
        Level::factory()->create(['number' => 7]);

        $level = $this->app->make(LevelService::class)->create([
            'name' => 'Novo nível',
            'summary' => null,
            'description' => 'Descrição do nível.',
            'danger_level' => DangerLevel::Safe,
        ], User::factory()->admin()->create());

        $this->assertSame(8, $level->number);
    }

    public function test_it_rejects_duplicate_numbers(): void
    {
        $user = User::factory()->admin()->create();
        Level::factory()->create(['number' => 12]);

        $this->expectException(DuplicateLevelNumberException::class);

        $this->app->make(LevelService::class)->create([
            'number' => 12,
            'name' => 'Nível duplicado',
            'description' => 'Descrição.',
            'danger_level' => DangerLevel::Low,
        ], $user);
    }

    public function test_it_sanitizes_tags_control_characters_and_whitespace(): void
    {
        $level = $this->app->make(LevelService::class)->create([
            'name' => "  <script>alert('x')</script>   Nome \n seguro  ",
            'summary' => "Resumo\tcom   espaços",
            'description' => "Descrição\r\n  limpa <b>agora</b>.",
            'danger_level' => DangerLevel::Moderate,
        ], User::factory()->admin()->create());

        $this->assertSame("alert('x') Nome seguro", $level->name);
        $this->assertSame('Resumo com espaços', $level->summary);
        $this->assertSame('Descrição limpa agora.', $level->description);
        $this->assertStringNotContainsString('<script>', $level->name);
    }
}
