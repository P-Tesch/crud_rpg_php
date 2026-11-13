<?php

namespace App\Entities;

/**
 * Values the player chose when using the action, exposed to Lua as `ctx.input`.
 */
readonly class StrategyInput
{
    public function __construct(
        /**
         * MP gasto na conjuração (nil fora de magias)
         */
        public ?int $cost = null,
    ) {}
}
