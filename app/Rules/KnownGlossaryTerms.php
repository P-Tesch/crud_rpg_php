<?php

namespace App\Rules;

use App\Helpers\GlossaryMarkup;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class KnownGlossaryTerms implements ValidationRule
{
    public const HELPER_TEXT = "Destaque efeitos do glossário com [[Efeito]] ou [[Efeito|texto exibido]].";

    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        if (!GlossaryMarkup::isWellFormed($value)) {
            $fail("Marcação de glossário incompleta: use [[Efeito]] ou [[Efeito|texto exibido]].");

            return;
        }

        $unknownTerms = GlossaryMarkup::unknownTerms($value);

        if ($unknownTerms !== []) {
            $fail("Efeito não encontrado no glossário: " . implode(", ", $unknownTerms) . ".");
        }
    }
}
