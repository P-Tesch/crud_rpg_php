<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Sonata;
use App\Models\System;
use App\Models\Miracle;
use App\Models\Advantage;
use App\Models\MysticEye;
use Illuminate\Database\Seeder;
use App\Models\ScriptureAbility;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        School::factory()
            ->hasSpells(fake()->numberBetween(1, 6))
            ->count(10)
            ->create();

        MysticEye::factory()
            ->count(10)
            ->create();

        Advantage::factory()
            ->count(10)
            ->create();

        ScriptureAbility::factory()
            ->count(10)
            ->create();

        Miracle::factory()
            ->count(10)
            ->create();

        Sonata::factory()
            ->count(10)
            ->hasSonataAbilities(fake()->numberBetween(1, 5))
            ->create();

        System::factory()
            ->hasSubsystems(fake()->numberBetween(2, 5))
            ->count(10)
            ->create();
    }
}
