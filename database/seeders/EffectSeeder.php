<?php

namespace Database\Seeders;

use App\Models\Effect;
use Illuminate\Database\Seeder;

class EffectSeeder extends Seeder
{
    /**
     * Seed the effects glossary from data/Effects.txt ("Name - Description" per line).
     * Upserts by name instead of truncating, since sheets, items and systems reference effects.
     */
    public function run(): void
    {
        $lines = file(__DIR__ . "/data/Effects.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            [$name, $description] = explode(" - ", $line, 2);

            Effect::updateOrCreate(["name" => trim($name)], ["description" => trim($description)]);
        }
    }
}
