<?php

namespace Database\Seeders;

use App\Models\Advantage;
use App\Models\MysticEye;
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

        MysticEye::factory()
            ->count(10)
            ->create();

        Advantage::factory()
            ->count(10)
            ->create();
    }
}
