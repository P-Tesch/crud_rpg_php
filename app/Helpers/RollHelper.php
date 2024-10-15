<?php

namespace App\Helpers;

class RollHelper {

    /**
     * Roll form a array of numbers
     * @param array<int, int> $modifiers
     * @return array<string, mixed>
     */
    public static function roll(array $modifiers) : array {
        $hits = 0;
        $results = [];
        $rolls = RollHelper::sumArray($modifiers);

        for ($i = 0; $i < $rolls; $i++) {
            $result = rand(1, 6);
            $results["rolls"][] = $result;
            $hits = $result >= 5 ? ++$hits : $hits;
        }

        $results["hits"] = $hits;

        return $results;
    }

    /**
     * Sum all number from array
     * @param array<int, int> $modifiers
     * @return int
     */
    private static function sumArray(array $modifiers) : int {
        $total = 0;

        foreach ($modifiers as $modifier) {
            $total += $modifier;
        }

        return $total;
    }
}
