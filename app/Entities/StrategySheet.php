<?php

namespace App\Entities;

/**
 * Read-only snapshot of a sheet handed to a Lua strategy, as `ctx.user` or an entry of `ctx.targets`.
 */
readonly class StrategySheet
{
    /**
     * @param array<string, int> $stats
     * @param array<string, int> $skills
     * @param array<string, int> $attributes
     * @param array<string, int> $maxAttributes
     * @param array<string, StrategyEffect> $effects
     */
    public function __construct(
        /**
         * id da ficha
         */
        public int $id,
        /**
         * nome do personagem
         */
        public string $name,
        /**
         * estatísticas: strength, dexterity, agility, endurance, intelligence, magic...
         *
         * @var array<string, int>
         */
        public array $stats,
        /**
         * perícias: melee, ranged, acrobatics, tenacity...
         *
         * @var array<string, int>
         */
        public array $skills,
        /**
         * atributos atuais: health, mana, movement...
         *
         * @var array<string, int>
         */
        public array $attributes,
        /**
         * atributos máximos: max_health, max_mana, max_movement...
         *
         * @var array<string, int>
         */
        public array $maxAttributes,
        /**
         * efeitos ativos por nome: { power, duration }
         *
         * @var array<string, StrategyEffect>
         */
        public array $effects,
    ) {}

    public static function fromSheet(SheetEntity $sheet): self
    {
        $effects = [];
        foreach ($sheet->effects ?? [] as $effect) {
            $effects[$effect["name"]] = new StrategyEffect($effect["power"], $effect["remaining_duration"]);
        }

        return new self(
            id: $sheet->id,
            name: $sheet->name,
            stats: $sheet->stats,
            skills: $sheet->skills,
            attributes: $sheet->attributes,
            maxAttributes: $sheet->maxAttributes,
            effects: $effects,
        );
    }
}
