<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lua strategy sandbox limits
    |--------------------------------------------------------------------------
    |
    | Limits applied to every LuaSandbox created by App\Services\Strategy\StrategyEngine.
    | The CPU limit is in seconds of CPU time, the memory limit in bytes.
    |
    */

    "cpu_limit" => (float) env("STRATEGY_CPU_LIMIT", 0.1),

    "memory_limit" => (int) env("STRATEGY_MEMORY_LIMIT", 8 * 1024 * 1024),

];
