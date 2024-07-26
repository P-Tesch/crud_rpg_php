<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        School::factory()
            ->hasSpells(fake()->numberBetween(1, 6))
            ->count(5)
            ->create();
    }
}
