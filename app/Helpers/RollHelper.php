<?php

namespace App\Helpers;
use App\Entities\SheetEntity;
use App\Models\Effect;
use App\Models\Strategy;

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
     * Applies a $strategy to a $user and it's $targets
     * @param array<string, mixed> $strategy
     * @param SheetEntity $user
     * @param list<SheetEntity> $targets
     * @param int $subject
     * @param int $modifier
     * @param int $difficulty
     * @return void
     */
    public static function proccessStrategy(array $strategy, SheetEntity &$user, array &$targets, int $subject = 0, int $modifier = 0, int $difficulty= 0) : void {
        $targets[] = $user;
        foreach ($strategy["tactics"] as $tactic) {
            $checkAmountDice = 0;
            $checkTargetDice = 0;
            $target = $targets[0];

            foreach ($tactic["check"]["checkAmount"] as $checkAmount) {
                $checkAmountDice += match ($checkAmount["amountType"]) {
                    "user" => array_key_exists($checkAmount["userAmount"], $user->skills) ? $user->skills[$checkAmount["userAmount"]] : $user->stats[$checkAmount["userAmount"]],
                    "fixed" => $checkAmount["fixedAmount"],
                    "target" => array_key_exists($checkAmount["targetAmount"], $target->skills) ? $target->skills[$checkAmount["targetAmount"]] : $target->stats[$checkAmount["targetAmount"]]
                };
            }

            $isDifficulty = false;
            foreach ($tactic["check"]["checkTarget"] as $checkTarget) {
                if ($checkTarget["targetType"] === "difficulty") {
                    $isDifficulty = true;
                }

                $checkTargetDice += match ($checkTarget["targetType"]) {
                    "user" => array_key_exists($checkTarget["userAmount"], $user->skills) ? $user->skills[$checkTarget["userAmount"]] : $user->stats[$checkTarget["userAmount"]],
                    "fixed" => $checkTarget["fixedAmount"],
                    "target" => array_key_exists($checkTarget["targetAmount"], $target->skills) ? $target->skills[$checkTarget["targetAmount"]] : $target->stats[$checkTarget["targetAmount"]],
                    "difficulty" => $difficulty
                };
            }

            $checkAmountHits = self::roll([$checkAmountDice + $modifier])["hits"];
            $checkTargetHits = $isDifficulty ? $checkTargetDice : self::roll([$checkTargetDice])["hits"];

            $success = $checkAmountHits >= $checkTargetHits;
            $diff = $checkAmountHits - $checkTargetHits;

            if ($success) {
                if (array_key_exists("damage", $tactic)) {
                    $damage = match ($tactic["damage"]["damageType"]) {
                        "modifier" => $subject
                    } * $tactic["damage"]["modifierMultiply"] + $tactic["damage"]["modifierAdd"];
                    $damage = $damage > 0 ? $damage : 0;
                    $target->attributes["health"] -= $damage;
                }

                if (array_key_exists("effect", $tactic)) {
                    // TODO
                }
            }

            dd($success, $target->attributes["health"], $checkAmountHits, $checkTargetHits);
        }
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
