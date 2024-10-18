<?php

namespace App\Helpers;
use App\Entities\SheetEntity;
use App\Models\Effect;

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
     * @return array<string, array<int | string, int>>
     */
    public static function processStrategy(array $strategy, SheetEntity &$user, array &$targets, int $subject = 0, int $modifier = 0, int $difficulty= 0) : array {
        $targets[] = $user;
        $userRoll = [];

        foreach ($strategy["tactics"] as $tactic) {
            $checkAmountDice = 0;
            $checkTargetDice = 0;
            $target = $targets[0];

            foreach ($tactic["check"]["checkAmount"] as $checkAmount) {
                $checkAmountDice += match ($checkAmount["amountType"]) {
                    "user" => array_key_exists($checkAmount["userAmount"], $user->skills) ? $user->skills[$checkAmount["userAmount"]] : $user->stats[$checkAmount["userAmount"]],
                    "fixed" => $checkAmount["fixedAmount"],
                    "target" => array_key_exists($checkAmount["targetAmount"], $target->skills) ? $target->skills[$checkAmount["targetAmount"]] : $target->stats[$checkAmount["targetAmount"]],
                    default => throw new \InvalidArgumentException("Invalid check type")
                };
            }

            $isDifficulty = false;
            foreach ($tactic["check"]["checkTarget"] as $checkTarget) {
                if ($checkTarget["targetType"] === "difficulty") {
                    $isDifficulty = true;
                }

                $checkTargetDice += match ($checkTarget["targetType"]) {
                    "user" => array_key_exists($checkTarget["userTarget"], $user->skills) ? $user->skills[$checkTarget["userTarget"]] : $user->stats[$checkTarget["userTarget"]],
                    "fixed" => $checkTarget["fixedTarget"],
                    "target" => array_key_exists($checkTarget["targetTarget"], $target->skills) ? $target->skills[$checkTarget["targetTarget"]] : $target->stats[$checkTarget["targetTarget"]],
                    "difficulty" => $difficulty,
                    default => throw new \InvalidArgumentException("Invalid check type")
                };
            }

            $userRoll = self::roll([$checkAmountDice + $modifier]);
            $targetRoll = !$isDifficulty ? self::roll([$checkTargetDice]) : null;

            $checkAmountHits = $userRoll["hits"];
            $checkTargetHits = $targetRoll["hits"] ?? $difficulty;

            $success = $checkAmountHits >= $checkTargetHits;
            $diff = $checkAmountHits - $checkTargetHits;

            if ($success) {
                if (array_key_exists("damage", $tactic)) {
                    $damage = match ($tactic["damage"]["damageType"]) {
                        "fixed" => $tactic["damage"]["fixedValue"],
                        "checkDiff" => $diff,
                        "element" => array_key_exists($tactic["damage"]["elementValue"], $user->skills) ? $user->skills[$tactic["damage"]["elementValue"]] : $user->stats[$tactic["damage"]["elementValue"]],
                        "elementRoll" => self::roll([array_key_exists($tactic["damage"]["elementValue"], $user->skills) ? $user->skills[$tactic["damage"]["elementValue"]] : $user->stats[$tactic["damage"]["elementValue"]]]),
                        "modifier" => $subject,
                        default => throw new \InvalidArgumentException("Invalid damage type")
                    } * $tactic["damage"]["modifierMultiply"] + $tactic["damage"]["modifierAdd"];

                    $damage = $damage > 0 ? (int) floor($damage) : 0;

                    $target->attributes["health"] -= $damage;
                }

                if (array_key_exists("heal", $tactic)) {
                    $heal = match ($tactic["heal"]["healType"]) {
                        "fixed" => $tactic["heal"]["fixedValue"],
                        "checkDiff" => $diff,
                        "element" => array_key_exists($tactic["heal"]["elementValue"], $user->skills) ? $user->skills[$tactic["heal"]["elementValue"]] : $user->stats[$tactic["heal"]["elementValue"]],
                        "elementRoll" => self::roll([array_key_exists($tactic["heal"]["elementValue"], $user->skills) ? $user->skills[$tactic["heal"]["elementValue"]] : $user->stats[$tactic["heal"]["elementValue"]]]),
                        "modifier" => $subject,
                        default => throw new \InvalidArgumentException("Invalid heal type")
                    } * $tactic["heal"]["modifierMultiply"] + $tactic["heal"]["modifierAdd"];

                    $heal = $heal > 0 ? (int) floor($heal) : 0;

                    $target->attributes["health"] += $heal;
                }

                if (array_key_exists("effect", $tactic)) {
                    $effect = Effect::findOrFail($tactic["effect"]["effect"]);

                    $effect->pivot_power = (int) floor(match ($tactic["effect"]["effectPowerType"]) {
                        //TODO
                        default => 0
                    } * $tactic["effect"]["modifierMultiplyPower"] + $tactic["effect"]["modifierAddPower"]);

                    $effect->pivot_remaining_duration = (int) floor(match ($tactic["effect"]["effectDurationType"]) {
                        //TODO
                        default => 0
                    } * $tactic["effect"]["modifierMultiplyDuration"] + $tactic["effect"]["modifierAddDuration"]);

                    $effects = array_filter($target->effects, fn (Effect $original) => $original->name === $effect->name && ($original->pivot_power >= $effect->pivot_power || $original->pivot_remaining_duration >= $effect->pivot_remaining_duration));

                    $effects[] = $effect;

                    $target->effects = $effects;
                }
            }
        }

        return ["user" => $userRoll, "target" => $targetRoll];
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
