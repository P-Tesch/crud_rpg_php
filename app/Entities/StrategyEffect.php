<?php

namespace App\Entities;

/**
 * An active effect on a sheet, exposed to Lua inside `effects`, keyed by the effect name.
 */
readonly class StrategyEffect
{
    public function __construct(
        /**
         * força do efeito
         */
        public ?int $power,
        /**
         * turnos restantes
         */
        public ?int $duration,
    ) {}
}
