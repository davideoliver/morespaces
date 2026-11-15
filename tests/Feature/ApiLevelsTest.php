<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\DangerLevel;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLevelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_session_authentication_and_returns_json(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized()->assertJsonPath('error', 'unauthenticated');
        $this->getJson('/api/v1/levels')->assertUnauthorized();
        $this->postJson('/api/v1/levels', [])->assertUnauthorized();
    }

    public function test_agent_can_read_levels_but_cannot_write_them(): void
    {
        $agent = User::factory()->agent()->create();
        $level = Level::factory()->create();

        $this->actingAs($agent)->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.role', 'agent');
        $this->actingAs($agent)->getJson('/api/v1/levels')->assertOk();
        $this->actingAs($agent)->getJson("/api/v1/levels/{$level->number}")->assertOk();

        $payload = $this->validPayload();
        $this->actingAs($agent)->postJson('/api/v1/levels', $payload)->assertForbidden();
        $this->actingAs($agent)->putJson("/api/v1/levels/{$level->number}", $payload)->assertForbidden();
        $this->actingAs($agent)->deleteJson("/api/v1/levels/{$level->number}")->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_a_level(): void
    {
        $admin = User::factory()->admin()->create();
        $payload = $this->validPayload();

        $created = $this->actingAs($admin)->postJson('/api/v1/levels', $payload);
        $created->assertCreated()->assertJsonPath('data.name', $payload['name']);
        $this->assertSame(
            route('api.v1.levels.show', ['level' => $created->json('data.number')]),
            $created->headers->get('Location'),
        );

        $number = $created->json('data.number');
        $this->actingAs($admin)->patchJson("/api/v1/levels/{$number}", [
            ...$payload,
            'name' => 'Nível atualizado',
        ])->assertOk()->assertJsonPath('data.name', 'Nível atualizado');

        $this->actingAs($admin)->deleteJson("/api/v1/levels/{$number}")->assertNoContent();
        $this->assertDatabaseMissing('levels', ['number' => $number]);
    }

    public function test_api_validates_payload_and_reports_missing_levels(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson('/api/v1/levels', [
            'number' => 10000,
            'name' => 'x',
            'description' => '',
            'danger_level' => 'unknown',
        ])->assertUnprocessable()
            ->assertJsonPath('error', 'validation_error')
            ->assertJsonStructure(['errors' => ['number', 'name', 'description', 'danger_level']]);

        $this->actingAs($admin)->getJson('/api/v1/levels/9999')
            ->assertNotFound()
            ->assertJsonPath('error', 'not_found');
    }

    public function test_api_rejects_duplicate_numbers_and_invalid_enum_values(): void
    {
        $admin = User::factory()->admin()->create();
        Level::factory()->create(['number' => 123]);

        $this->actingAs($admin)->postJson('/api/v1/levels', [
            'number' => 123,
            'name' => 'Nível inválido',
            'description' => 'Descrição válida.',
            'danger_level' => 'unknown',
        ])->assertUnprocessable()
            ->assertJsonPath('error', 'validation_error')
            ->assertJsonStructure(['errors' => ['number', 'danger_level']]);
    }

    public function test_level_index_includes_pagination_meta_and_searches(): void
    {
        $agent = User::factory()->agent()->create();
        Level::factory()->create(['name' => 'Área API especial']);
        Level::factory()->create(['name' => 'Outro nível']);

        $this->actingAs($agent)->getJson('/api/v1/levels?q=especial&page=1')
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.0.name', 'Área API especial');
    }

    /** @return array<string, string> */
    private function validPayload(): array
    {
        return [
            'number' => '123',
            'name' => 'Nível API',
            'summary' => 'Resumo da API',
            'description' => 'Descrição da API',
            'danger_level' => DangerLevel::Low->value,
        ];
    }
}
