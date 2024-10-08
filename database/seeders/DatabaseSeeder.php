<?php

namespace Database\Seeders;

use App\Models\Advantage;
use App\Models\Miracle;
use App\Models\MysticEye;
use App\Models\ScriptureAbility;
use App\Models\Sonata;
use App\Models\SonataAbility;
use App\Models\Spell;
use App\Models\School;
use App\Models\Subsystem;
use App\Models\System;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     */
    public function run(): void
    {
        $this->seedSystemsAndSubystems();
        $this->seedAdvantages();
        $this->seedScriptureAbilities();
        $this->seedMiracles();
        $this->seedSonatasAndSonataAbilities();
        $this->seedMysticEyes();
        $this->seedSchoolsAndSpells();
        $this->seedAdminUsers();
    }

    /**
     * Seed the systems and subsystems tables
     * @return void
     */
    private function seedSystemsAndSubystems() : void {
        System::truncate();
        Subsystem::truncate();

        $lines = file(__DIR__ . "/data/Systems.txt");
        $lastSystem = null;

        foreach ($lines as $line) {
            $line = str_replace("\n", "", $line);
            if (!preg_match("/(Sistema\s-\s|)(.+?(?=:\s)):\s(.+)/", $line, $matches)) {
                continue;
            }

            if ($matches[1] != "") {
                $lastSystem = new System([
                    "name" => $matches[2],
                    "description" => $matches[3]
                ]);

                $lastSystem->save();
            } else {
                (new Subsystem([
                    "name" => $matches[2],
                    "description" => $matches[3],
                    "system_id" => $lastSystem->id
                ]))->save();
            }
        }
    }

    /**
     * Seed the advantages table
     * @return void
     */
    private function seedAdvantages() : void {
        Advantage::truncate();

        $lines = file(__DIR__ . "/data/Advantages.txt");

        foreach ($lines as $line) {
            $line = str_replace("\n", "", $line);
            if (!preg_match("/([A-zÀ-ú ]+)(?=:|(\d):)(?:.*:\s)(.+)\(Custo\s(\d+|-\d+)(?:.*((?<=\()[a-z]+)|).*/", $line, $matches)) {
                continue;
            }

            (new Advantage([
                "name" => $matches[1],
                "description" => $matches[3],
                "level" => $matches[2] != "" ? $matches[2] : 1,
                "cost" => (int) $matches[4],
                "class" => $matches[5] ?? null
            ]))->save();
        }
    }

    /**
     * Seed the scripture abilities table
     * @return void
     */
    private function seedScriptureAbilities() : void {
        ScriptureAbility::truncate();

        $lines = file(__DIR__ . "/data/ScriptureAbilities.txt");

        foreach ($lines as $line) {
            $line = str_replace("\n", "", $line);
            if (!preg_match("/(.+(?=:\s)):\s(.+?(?=\(Máximo|\(Custo))(?>.*((?<=\(Máximo\s)\d+)|).*((?<=\(Custo\s|\(Custo\smínimo\s)\d+)/", $line, $matches)) {
                continue;
            }

            for ($i = 1; $i <= ($matches[3] != "" ? $matches[3] : 1); $i++) {
                (new ScriptureAbility([
                    "name" => $matches[1],
                    "description" => $matches[2],
                    "level" => $i,
                    "cost" => $matches[4]
                ]))->save();
            }
        }
    }

    /**
     * Seed the miracles table
     * @return void
     */
    private function seedMiracles() : void {
        Miracle::truncate();

        $lines = file(__DIR__ . "/data/Miracles.txt");

        foreach ($lines as $line) {
            $line = str_replace("\n", "", $line);

            switch (true) {
                case $line == "":
                    break;

                default:
                    $lineArray = explode(" - ", $line);
                    $lineArray2 = explode("(Custo ", $lineArray[1]);
                    (new Miracle([
                        "name" => $lineArray[0],
                        "description" => $lineArray2[0],
                        "cost" => (int) str_replace(")", "", $lineArray2[1]),
                        "strategy" => null
                    ]))->save();
            }
        }
    }

    /**
     * Seed the sonatas and sonata_abilities tables
     * @return void
     */
    private function seedSonatasAndSonataAbilities() : void {
        Sonata::truncate();
        SonataAbility::truncate();

        $lines = file(__DIR__ . "/data/Sonatas.txt");

        $lastSonata = null;
        $lastAbility = null;
        foreach ($lines as $line) {
            $line = str_replace("\n", "", $line);

            switch (true) {
                case $line == "":
                    break;

                case str_starts_with($line, "Sonata"):
                    $lineArray = explode(": ", $line);
                    $lastSonata = new Sonata([
                        "name" => explode(" - ", $lineArray[0])[1],
                        "description" => $lineArray[1]
                    ]);

                    $lastSonata->save();
                    break;

                case str_starts_with($line, "Custo"):
                    $lineArray = explode(": ", $line);
                    $costAndMax = explode("[", $lineArray[1]);

                    $lastAbility->cost = $costAndMax[0];
                    $lastAbility->level = 1;

                    $abilities = [];
                    $abilities[] = $lastAbility;

                    if (array_key_exists(1, $costAndMax)) {
                        for ($i = 2; $i <= (int) preg_replace("/[^\d]/", "", $costAndMax[1]); $i++) {
                            $ability = clone $lastAbility;
                            $ability->level = $i;
                            $abilities[] = $ability;
                        }
                    }

                    foreach ($abilities as $ability) {
                        $ability->save();
                    }
                    break;

                default:
                    $lineArray = explode(": ", $line);

                    $lastAbility = new SonataAbility([
                        "name" => $lineArray[0],
                        "description" => $lineArray[1],
                        "level" => null,
                        "cost" => null,
                        "strategy" => null,
                        "sonata_id" => $lastSonata->id
                    ]);
            }
        }
    }

    /**
     * Seed the mystic_eyes table
     * @return void
     */
    private function seedMysticEyes() : void {
        MysticEye::truncate();

        $lines = file(__DIR__ . "/data/Eyes.txt");

        $eyeArgs = [];
        foreach ($lines as $line) {
            switch (true) {
                case str_starts_with($line, "Olhos"):
                    if (!empty($eyeArgs)) {
                        (new MysticEye($eyeArgs))->save();
                        $eyeArgs = [];
                    }
                    $eyeArgs["name"] = trim($line);
                    break;

                case str_starts_with($line, "Passivo"):
                    $eyeArgs["passive"] = trim(explode(": ", preg_replace("/\(Custo\s\d+\)/", "", $line))[1]);

                    $hasCost = preg_match("/\(Custo\s\d+\)/", $line, $cost);
                    if ($hasCost) {
                        preg_match("/\d+/", $cost[0], $costNumber);
                        $eyeArgs["cost"] = $costNumber[0];
                    }

                    break;

                case str_starts_with($line, "Ativo"):
                    $eyeArgs["active"] = trim(explode(": ", preg_replace("/\(Custo\s\d+\)/", "", $line))[1]);

                    $hasCost = preg_match("/\(Custo\s\d+\)/", $line, $cost);
                    if ($hasCost) {
                        preg_match("/\d+/", $cost[0], $costNumber);
                        $eyeArgs["cost"] = $costNumber[0];
                    }

                    break;

                case str_starts_with($line, "Cooldown"):
                    $eyeArgs["cooldown"] = explode(": ", $line)[1];
            }
        }

        (new MysticEye($eyeArgs))->save();
    }

    /**
     * Seed the schools and spells tables
     * @return void
     */
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

    private function seedAdminUsers() : void {
        (new User([
            "login" => $_ENV["ADMIN_LOGIN"],
            "password" => $_ENV["ADMIN_PASSWORD"],
            "is_admin" => true
        ]))->save();
    }

    /**
     * Support function to get the sum of every integer from 1 up to $number
     * @param int $number
     * @return int
     */
    private function summation(int $number) {
        $total = 0;
        for ($i = 1; $i <= $number; $i++) {
            $total += $i;
        }
        return $total;
    }
}
