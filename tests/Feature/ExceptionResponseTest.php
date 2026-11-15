<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ExceptionResponseTest extends TestCase
{
    public function test_not_found_json_uses_standardized_envelope(): void
    {
        $response = $this->getJson('/rota-que-nao-existe');

        $response->assertStatus(404)
            ->assertJsonStructure(['status', 'error', 'message'])
            ->assertJsonPath('error', 'not_found');
    }

    public function test_not_found_html_uses_themed_view(): void
    {
        $this->get('/rota-que-nao-existe')
            ->assertStatus(404)
            ->assertSee('Este espaço não foi encontrado.');
    }

    public function test_validation_json_uses_standardized_envelope(): void
    {
        $this->warmRoutes();
        $response = $this->postJson(route('login'), []);

        $response->assertStatus(422)
            ->assertJsonStructure(['status', 'error', 'message', 'errors'])
            ->assertJsonPath('error', 'validation_error')
            ->assertJsonPath('status', 422);
    }

    public function test_internal_error_hides_debug_details_when_debug_is_off(): void
    {
        config(['app.debug' => false]);
        $this->registerFailingRoute();
        $this->warmRoutes();

        $response = $this->getJson('/test-forced-500');

        $response->assertStatus(500)
            ->assertJsonMissingPath('debug')
            ->assertJsonPath('error', 'internal_server_error');
    }

    public function test_internal_error_includes_debug_details_when_debug_is_on(): void
    {
        config(['app.debug' => true]);
        $this->registerFailingRoute();
        $this->warmRoutes();

        $response = $this->getJson('/test-forced-500');

        $response->assertStatus(500)
            ->assertJsonStructure(['debug' => ['exception', 'file', 'line']])
            ->assertJsonPath('error', 'internal_server_error');
    }

    private function registerFailingRoute(): void
    {
        Route::get('/test-forced-500', function (): never {
            throw new RuntimeException('detalhe técnico que não deve vazar');
        });
    }

    private function warmRoutes(): void
    {
        $this->get('/');
    }
}
