<?php

namespace Database\Seeders;

use App\Models\Spell;
use App\Models\School;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedSchoolsAndSpells();
    }

    private function seedSchoolsAndSpells() : void {
        DB::table("school_spell")->truncate();
        School::truncate();
        Spell::truncate();

        $lines = file(__DIR__ . "/data/Magic.txt");

        $schools = [];
        $schoolArgs = null;
        $baseCost = null;
        $spells = [];

        foreach ($lines as $line) {
            $line = str_replace("\n", "", $line);

            switch (true) {
                case str_starts_with($line, "Escola:"):
                    if (!empty($spells)) {
                        $school = $schools[$schoolArgs["name"] . " " . $schoolArgs["level"]];
                        $school->spells()->saveMany($spells);
                    }

                    $lineArray = explode(": ", $line);
                    $schoolArgs = [
                        "name" => $lineArray[1],
                        "description" => $lineArray[2]
                    ];
                    $baseCost = $lineArray[3];
                    $spells = [];

                    continue 2;

                case str_starts_with($line, "Nível"):
                    if (!empty($spells)) {
                        $school = $schools[$schoolArgs["name"] . " " . $schoolArgs["level"]];
                        $school->spells()->saveMany($spells);
                    }

                    $schoolArgs["level"] = (int) explode(" ", $line)[1];
                    $schoolArgs["cost"] = $this->summation($schoolArgs["level"]) * $baseCost;
                    $school = new School($schoolArgs);
                    $school->save();

                    $schools[$schoolArgs["name"] . " " . $schoolArgs["level"]] = $school;
                    continue 2;

                case preg_match("/[A-Za-z]/", $line):
                    $lineArray = explode(": ", $line);
                    $spellArgs = [
                        "name" => $lineArray[0],
                        "description" => $lineArray[1],
                        "type" => $lineArray[2] ?? null,
                        "strategy" => $lineArray[3] ?? null
                    ];

                    $spell = new Spell($spellArgs);
                    $spell->save();
                    $spells[] = $spell;
                    continue 2;

                default:
                    continue 2;

            }
        }

        $school = $schools[$schoolArgs["name"] . " " . $schoolArgs["level"]];
        $school->spells()->saveMany($spells);
    }

    private function summation(int $number) {
        $total = 0;
        for ($i = 1; $i <= $number; $i++) {
            $total += $i;
        }
        return $total;
    }
}
