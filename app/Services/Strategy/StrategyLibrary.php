<?php

namespace App\Services\Strategy;

use App\Helpers\RollHelper;
use App\Models\Effect;
use LuaSandboxRuntimeError;

class StrategyLibrary
{
    /**
     * rpg.roll(dados, rótulo?) -> { hits, rolls }
     * @return array{array{hits: int, rolls: array<int, int>}}
     */
    public static function roll(StrategyRun $run, mixed $dice = null, mixed $label = null): array
    {
        return [self::rollDice($run, $dice, $label)];
    }

    /**
     * rpg.check(dados, dificuldade, rótulo?) -> { success, diff, hits, rolls }
     * @return array{array{success: bool, diff: int, hits: int, rolls: array<int, int>}}
     */
    public static function check(StrategyRun $run, mixed $dice = null, mixed $difficulty = null, mixed $label = null): array
    {
        $userRoll = self::rollDice($run, $dice, $label);
        $diff = $userRoll["hits"] - self::integer($difficulty, "difficulty");

        return [["success" => $diff >= 0, "diff" => $diff, "hits" => $userRoll["hits"], "rolls" => $userRoll["rolls"]]];
    }

    /**
     * rpg.contest(dados, dadosOponente, rótulo?) -> { success, diff, hits, opposingHits, ... }
     * @return array{array{success: bool, diff: int, hits: int, rolls: array<int, int>, opposingHits: int, opposingRolls: array<int, int>}}
     */
    public static function contest(StrategyRun $run, mixed $dice = null, mixed $opposingDice = null, mixed $label = null): array
    {
        $userRoll = self::rollDice($run, $dice, $label);
        $opposingRoll = self::rollDice($run, $opposingDice, is_string($label) ? $label . " (oposição)" : null);
        $diff = $userRoll["hits"] - $opposingRoll["hits"];

        return [[
            "success" => $diff >= 0,
            "diff" => $diff,
            "hits" => $userRoll["hits"],
            "rolls" => $userRoll["rolls"],
            "opposingHits" => $opposingRoll["hits"],
            "opposingRolls" => $opposingRoll["rolls"],
        ]];
    }

    /**
     * rpg.damage(alvo, n) — alvo: "user" ou índice
     * @return array{}
     */
    public static function damage(StrategyRun $run, mixed $who = null, mixed $amount = null): array
    {
        $sheet = $run->target($who);
        $amount = max(0, self::integer($amount, "amount"));
        $run->defer(function () use ($sheet, $amount): void {
            $sheet->attributes["health"] = ($sheet->attributes["health"] ?? 0) - $amount;
        });

        return [];
    }

    /**
     * rpg.heal(alvo, n)
     * @return array{}
     */
    public static function heal(StrategyRun $run, mixed $who = null, mixed $amount = null): array
    {
        $sheet = $run->target($who);
        $amount = max(0, self::integer($amount, "amount"));
        $run->defer(function () use ($sheet, $amount): void {
            $sheet->attributes["health"] = ($sheet->attributes["health"] ?? 0) + $amount;
        });

        return [];
    }

    /**
     * rpg.modify(alvo, atributo, delta)
     * @return array{}
     */
    public static function modify(StrategyRun $run, mixed $who = null, mixed $attribute = null, mixed $delta = null): array
    {
        $sheet = $run->target($who);
        if (!is_string($attribute) || !array_key_exists($attribute, $sheet->attributes)) {
            throw new LuaSandboxRuntimeError("unknown attribute '" . (is_scalar($attribute) ? $attribute : gettype($attribute)) . "'");
        }
        $delta = self::integer($delta, "delta");
        $run->defer(function () use ($sheet, $attribute, $delta): void {
            $sheet->attributes[$attribute] += $delta;
        });

        return [];
    }

    /**
     * rpg.applyEffect(alvo, nome, força?, duração?)
     * @return array{}
     */
    public static function applyEffect(StrategyRun $run, mixed $who = null, mixed $name = null, mixed $power = null, mixed $duration = null): array
    {
        $sheet = $run->target($who);
        $effect = self::findEffect($name);
        $power = $power === null ? null : self::integer($power, "power");
        $duration = $duration === null ? null : self::integer($duration, "duration");
        $run->defer(function () use ($sheet, $effect, $power, $duration): void {
            $current = $sheet->effects[$effect->id] ?? null;
            if ($current === null || self::isStronger($power, $duration, $current["power"], $current["remaining_duration"])) {
                $sheet->effects[$effect->id] = ["name" => $effect->name, "power" => $power, "remaining_duration" => $duration];
            }
        });

        return [];
    }

    /**
     * rpg.removeEffect(alvo, nome)
     * @return array{}
     */
    public static function removeEffect(StrategyRun $run, mixed $who = null, mixed $name = null): array
    {
        $sheet = $run->target($who);
        $effect = self::findEffect($name);
        $run->defer(function () use ($sheet, $effect): void {
            unset($sheet->effects[$effect->id]);
        });

        return [];
    }

    /**
     * rpg.log(texto)
     * @return array{}
     */
    public static function log(StrategyRun $run, mixed $message = null): array
    {
        $run->result->addLog(is_scalar($message) ? (string) $message : gettype($message));

        return [];
    }

    /**
     * @return array{hits: int, rolls: array<int, int>}
     */
    private static function rollDice(StrategyRun $run, mixed $dice, mixed $label): array
    {
        $roll = RollHelper::roll([max(0, self::integer($dice, "dice"))]);
        /** @var list<int> $dieResults */
        $dieResults = $roll["rolls"] ?? [];
        $hits = (int) $roll["hits"];
        $run->result->addRoll(is_string($label) ? $label : null, $hits, $dieResults);

        return ["hits" => $hits, "rolls" => self::luaList($dieResults)];
    }

    /**
     * A re-applied effect replaces the current one when it has more power, or equal power and a longer duration.
     */
    private static function isStronger(?int $power, ?int $duration, ?int $currentPower, ?int $currentDuration): bool
    {
        if (($power ?? 0) !== ($currentPower ?? 0)) {
            return ($power ?? 0) > ($currentPower ?? 0);
        }

        return ($duration ?? 0) > ($currentDuration ?? 0);
    }

    private static function findEffect(mixed $name): Effect
    {
        $effect = is_string($name) ? Effect::where("name", $name)->first() : null;
        if ($effect === null) {
            throw new LuaSandboxRuntimeError("unknown effect '" . (is_scalar($name) ? $name : gettype($name)) . "'");
        }

        return $effect;
    }

    private static function integer(mixed $value, string $name): int
    {
        if (!is_int($value) && !is_float($value)) {
            throw new LuaSandboxRuntimeError("'{$name}' must be a number");
        }

        return (int) floor($value);
    }

    /**
     * @param list<int> $values
     * @return array<int, int>
     */
    private static function luaList(array $values): array
    {
        return empty($values) ? [] : array_combine(range(1, count($values)), $values);
    }
}
