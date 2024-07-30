<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MysticEye>
 */
class MysticEyeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->sentence(),
            "passive" => fake()->paragraph(),
            "active" => fake()->paragraph(),
            "cooldown" => fake()->numberBetween(1, 20),
            "cost" => fake()->numberBetween(5, 30),
            "active_strategy" => null,
            "passive_strategy" => null
        ];
    }
}
