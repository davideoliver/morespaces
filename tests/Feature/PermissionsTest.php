<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\ContactMessage;
use App\Models\Level;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('permissionMatrix')]
    public function test_permission_matrix(string $role, string $action, int $expectedStatus): void
    {
        $user = match ($role) {
            'visitor' => null,
            'member' => User::factory()->member()->create(),
            'agent' => User::factory()->agent()->create(),
            'admin' => User::factory()->admin()->create(),
        };

        if ($action === 'files.viewAny') {
            $response = $user === null
                ? $this->get(route('files'))
                : $this->actingAs($user)->get(route('files'));

            $response->assertStatus($expectedStatus);

            return;
        }

        $arguments = match ($action) {
            'levels.viewAny', 'levels.create' => [Level::class],
            'levels.view', 'levels.update', 'levels.delete' => [Level::class, new Level],
            'contacts.viewAny' => [ContactMessage::class],
            'contacts.delete' => [ContactMessage::class, new ContactMessage],
        };
        $ability = substr($action, strpos($action, '.') + 1);
        $allowed = Gate::forUser($user)->allows($ability, $arguments);

        $this->assertSame($expectedStatus === 200, $allowed);
    }

    public static function permissionMatrix(): array
    {
        $matrix = [];

        foreach (['visitor', 'member', 'agent', 'admin'] as $role) {
            $matrix[] = [$role, 'files.viewAny', match ($role) {
                'visitor' => 302,
                'agent', 'admin' => 200,
                default => 403,
            }];

            foreach ([
                'levels.viewAny',
                'levels.view',
            ] as $action) {
                $matrix[] = [$role, $action, in_array($role, ['agent', 'admin'], true) ? 200 : 403];
            }

            foreach (['levels.create', 'levels.update', 'levels.delete', 'contacts.viewAny', 'contacts.delete'] as $action) {
                $matrix[] = [$role, $action, $role === 'admin' ? 200 : 403];
            }
        }

        return $matrix;
    }

    public function test_set_role_command_promotes_a_member_and_updates_the_menu(): void
    {
        $member = User::factory()->create(['username' => 'promotable_member']);

        $this->assertSame(0, Artisan::call('morespaces:set-role', [
            'username' => $member->username,
            'role' => 'agent',
        ]));
        $this->assertSame(Role::Agent, $member->refresh()->role);

        $this->actingAs($member)->get(route('home'))
            ->assertSee(route('files'), false);
    }
}
