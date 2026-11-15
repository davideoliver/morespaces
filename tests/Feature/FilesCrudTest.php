<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\DangerLevel;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilesCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_complete_the_level_crud(): void
    {
        $admin = User::factory()->admin()->create();
        $payload = [
            'name' => 'Nível criado',
            'summary' => 'Resumo criado',
            'description' => 'Descrição criada',
            'danger_level' => DangerLevel::Low->value,
        ];

        $response = $this->actingAs($admin)->post(route('files.store'), $payload);
        $level = Level::query()->where('name', 'Nível criado')->firstOrFail();

        $response->assertRedirect(route('files.show', $level));
        $this->actingAs($admin)->get(route('files.index'))->assertOk()->assertSee('Nível criado');
        $this->actingAs($admin)->get(route('files.show', $level))->assertOk()->assertSee('Descrição criada');

        $this->actingAs($admin)->put(route('files.update', $level), [
            ...$payload,
            'name' => 'Nível atualizado',
        ])->assertRedirect(route('files.show', ['level' => $level->number]));

        $this->assertDatabaseHas('levels', ['number' => $level->number, 'name' => 'Nível atualizado']);

        $this->actingAs($admin)->delete(route('files.destroy', $level))
            ->assertRedirect(route('files.index'));
        $this->assertDatabaseMissing('levels', ['id' => $level->id]);
    }

    public function test_invalid_payload_returns_validation_errors(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('files.store'), [
            'number' => 10000,
            'name' => 'x',
            'description' => '',
            'danger_level' => 'unknown',
        ])->assertSessionHasErrors(['number', 'name', 'description', 'danger_level']);
    }

    public function test_duplicate_level_number_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Level::factory()->create(['number' => 42]);

        $this->actingAs($admin)->post(route('files.store'), [
        'number' => 42,
        'name' => 'Nível duplicado',
        'description' => 'Descrição válida.',
        'danger_level' => DangerLevel::Low->value,
        ])->assertSessionHasErrors('number');
    }

    public function test_payload_over_limits_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('files.store'), [
        'name' => str_repeat('N', 121),
        'summary' => str_repeat('S', 256),
        'description' => str_repeat('D', 5001),
        'danger_level' => DangerLevel::Low->value,
        ])->assertSessionHasErrors(['name', 'summary', 'description']);
    }

    public function test_agent_can_view_but_cannot_mutate_levels(): void
    {
        $agent = User::factory()->agent()->create();
        $level = Level::factory()->create();

        $this->actingAs($agent)->get(route('files.show', $level))->assertOk();
        $this->actingAs($agent)->get(route('files.create'))->assertForbidden();
        $this->actingAs($agent)->put(route('files.update', $level), [])->assertForbidden();
        $this->actingAs($agent)->delete(route('files.destroy', $level))->assertForbidden();
    }

    public function test_search_treats_like_wildcards_as_literal_text(): void
    {
        $agent = User::factory()->agent()->create();
        $matching = Level::factory()->create(['name' => 'Área 100% segura']);
        Level::factory()->create(['name' => 'Área 1000 segura']);

        $this->actingAs($agent)->get(route('files.index', ['q' => '100%']))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee('Área 1000 segura');
    }
}
