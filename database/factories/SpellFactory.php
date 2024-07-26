<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Spell>
 */
class SpellFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->word,
            "description" => fake()->sentence(),
            "type" => ["PROJECTILE", "DIRECT", null][fake()->numberBetween(0, 2)],
            "strategy" => null
        ];
    }
}
