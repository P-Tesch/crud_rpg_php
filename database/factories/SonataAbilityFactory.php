<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SonataAbility>
 */
class SonataAbilityFactory extends Factory
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
            "level" => fake()->numberBetween(1, 3),
            "cost" => fake()->numberBetween(5, 20),
            "strategy" => null
        ];
    }
}
