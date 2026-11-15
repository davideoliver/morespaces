<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DangerLevel;
use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Level>
 */
class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numberBetween(0, 65535),
            'name' => $this->faker->words(3, true),
            'summary' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'danger_level' => DangerLevel::Moderate,
            'created_by' => User::factory(),
        ];
    }
}
