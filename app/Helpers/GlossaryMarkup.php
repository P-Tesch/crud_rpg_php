<?php

namespace App\Helpers;

use App\Models\Effect;

class GlossaryMarkup
{
    public const TERM_PATTERN = '/\[\[([^\[\]|]+)(?:\|([^\[\]]+))?\]\]/u';

    /**
     * @return list<string>
     */
    public static function referencedTerms(string $text): array
    {
        preg_match_all(self::TERM_PATTERN, $text, $matches);

        return array_values(array_unique(array_map(trim(...), $matches[1])));
    }

    public static function isWellFormed(string $text): bool
    {
        return substr_count($text, "[[") === preg_match_all(self::TERM_PATTERN, $text);
    }

    /**
     * @return list<string>
     */
    public static function unknownTerms(string $text): array
    {
        $terms = self::referencedTerms($text);

        if ($terms === []) {
            return [];
        }

        $knownNames = array_map(mb_strtolower(...), Effect::query()->pluck("name")->all());

        return array_values(array_filter(
            $terms,
            fn (string $term): bool => !in_array(mb_strtolower($term), $knownNames, true),
        ));
    }
}
