<?php

namespace App\Entities;

/**
 * Everything a Lua strategy did during one run: the dice it rolled
 * and the messages it logged. Sheet changes are applied directly to the SheetEntity objects.
 */
class StrategyResult
{
    /** @var list<array{label: ?string, hits: int, rolls: list<int>}> */
    private array $rolls = [];

    /** @var list<string> */
    private array $logs = [];

    /**
     * @param list<int> $rolls
     */
    public function addRoll(?string $label, int $hits, array $rolls): void
    {
        $this->rolls[] = [
            "label" => $label,
            "hits" => $hits,
            "rolls" => $rolls,
        ];
    }

    public function addLog(string $message): void
    {
        $this->logs[] = $message;
    }

    /**
     * The first roll made by the script, in the shape the roll history UI already renders.
     *
     * @return ?array{rolls: list<int>, hits: int}
     */
    public function getMainRoll(): ?array
    {
        if (empty($this->rolls)) {
            return null;
        }

        return ["rolls" => $this->rolls[0]["rolls"], "hits" => $this->rolls[0]["hits"]];
    }

    /**
     * @return array{rolls: list<array{label: ?string, hits: int, rolls: list<int>}>, logs: list<string>}
     */
    public function toArray(): array
    {
        return [
            "rolls" => $this->rolls,
            "logs" => $this->logs,
        ];
    }
}
