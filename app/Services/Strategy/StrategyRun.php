<?php

namespace App\Services\Strategy;

use App\Entities\SheetEntity;
use App\Entities\StrategyResult;
use Closure;
use LuaSandboxRuntimeError;

/**
 * State of a single strategy execution, handed to every StrategyLibrary function.
 * Sheet changes are queued and only applied once the whole script has succeeded.
 */
class StrategyRun
{
    /** @var list<Closure(): void> */
    private array $pendingChanges = [];

    /**
     * @param list<SheetEntity> $targets
     */
    public function __construct(
        public readonly SheetEntity $user,
        public readonly array $targets,
        public readonly StrategyResult $result = new StrategyResult(),
    ) {}

    /**
     * Resolve a Lua target reference: "user" or a 1-based target index.
     *
     * @throws LuaSandboxRuntimeError
     */
    public function target(mixed $who): SheetEntity
    {
        if ($who === "user") {
            return $this->user;
        }

        $index = is_numeric($who) ? (int) $who : 0;
        if (!isset($this->targets[$index - 1])) {
            throw new LuaSandboxRuntimeError("invalid target '" . (is_scalar($who) ? $who : gettype($who)) . "': use \"user\" or a target index from 1 to " . count($this->targets));
        }

        return $this->targets[$index - 1];
    }

    /**
     * @param Closure(): void $change
     */
    public function defer(Closure $change): void
    {
        $this->pendingChanges[] = $change;
    }

    public function applyChanges(): void
    {
        foreach ($this->pendingChanges as $applyChange) {
            $applyChange();
        }

        $this->pendingChanges = [];
    }
}
