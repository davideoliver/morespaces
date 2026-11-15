<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\DangerLevel;
use App\Enums\Role;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    #[DataProvider('roleValues')]
    public function test_role_accepts_only_declared_values(string $value, Role $expected): void
    {
        $this->assertSame($expected, Role::from($value));
    }

    /** @return array<string, array{string, Role}> */
    public static function roleValues(): array
    {
        return [
            'member' => [Role::Member->value, Role::Member],
            'agent' => [Role::Agent->value, Role::Agent],
            'admin' => [Role::Admin->value, Role::Admin],
        ];
    }

    public function test_invalid_danger_level_is_rejected(): void
    {
        $this->expectException(\ValueError::class);

        DangerLevel::from('unknown');
    }
}
