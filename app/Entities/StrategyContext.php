<?php

namespace App\Entities;

/**
 * The argument a Lua strategy receives as `ctx`. Property docblocks are shown as editor completions.
 */
readonly class StrategyContext
{
    /**
     * @param list<StrategySheet> $targets
     */
    public function __construct(
        /**
         * ficha do usuário
         */
        public StrategySheet $user,
        /**
         * lista de fichas alvo (1..n), mesmos campos de ctx.user
         *
         * @var list<StrategySheet>
         */
        public array $targets,
        /**
         * valores da ação
         */
        public StrategyInput $input,
    ) {}
}
