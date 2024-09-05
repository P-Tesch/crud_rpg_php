<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\System>
 */
class SystemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->word(),
            "description" => fake()->paragraph(),
            "damage" => fake()->numberBetween(1, 5),
            "strategy_passive" => null,
            "strategy_active" => null,
            "strategy_burn" => null
        ];
    }
}
