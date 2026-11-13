<?php

namespace App\Rules;

use App\Services\Strategy\StrategyEngine;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidLuaStrategy implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || blank($value)) {
            return;
        }

        if (!class_exists(\LuaSandbox::class)) {
            $fail("A extensão LuaSandbox não está instalada no servidor.");

            return;
        }

        $error = app(StrategyEngine::class)->syntaxError($value);
        if ($error !== null) {
            $fail("Erro de sintaxe Lua: {$error}");
        }
    }
}
