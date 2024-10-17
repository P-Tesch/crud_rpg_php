<?php

namespace App\Filament\Components;

use App\Models\Effect;
use Filament\Forms\Get;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Builder\Block;

class StrategyBuilder {

    public static function make() : Field {
        return Repeater::make("strategy.value")
            ->schema([
                Builder::make("tactic")
                ->blockNumbers(false)
                ->collapsible()
                ->blocks([
                    Block::make("target")
                        ->key("target")
                        ->schema([
                            Select::make("type")
                                ->options([
                                    "self" => "Usuário",
                                    "single" => "Único",
                                    "multiple" => "AOE"
                                ])
                                ->required()
                                ->label("Tipo de alvo")
                        ])
                        ->label("Alvo"),

                    Block::make("effect")
                        ->schema([
                            Select::make("effect")
                                ->options(Effect::all()->pluck("name", "id"))
                                ->preload()
                                ->searchable()
                                ->required(true)
                                ->label("Efeito"),

                            Select::make("effectPowerType")
                                ->options([
                                    "fixed" => "Fixo",
                                    "checkDiff" => "Diferença de checagem",
                                    "element" => "Elemento",
                                    "elementRoll" => "Elemento (Rolagem)",
                                    "modifier" => "Modificador"
                                ])
                                ->label("Tipo"),

                            TextInput::make("fixedValue")
                                ->numeric()
                                ->hidden(fn (Get $get) => $get("effectPowerType") !== "fixed" || $get("effectPowerType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Valor"),

                            Select::make("elementValue")
                                ->hidden(fn (Get $get) => ($get("effectPowerType") !== "element" && $get("effectPowerType") !== "elementRoll") || $get("effectPowerType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Elemento"),

                            TextInput::make("modifierAddPower")
                                ->numeric()
                                ->default(0)
                                ->label("Modificador final (Aditivo)"),

                            TextInput::make("modifierMultiplyPower")
                                ->numeric()
                                ->default(1)
                                ->label("Modificador final (Multiplicativo)"),

                                Select::make("effectDurationType")
                                ->options([
                                    "fixed" => "Fixo",
                                    "checkDiff" => "Diferença de checagem",
                                    "element" => "Elemento",
                                    "elementRoll" => "Elemento (Rolagem)",
                                    "modifier" => "Modificador"
                                ])
                                ->label("Tipo"),

                            TextInput::make("fixedValue")
                                ->numeric()
                                ->hidden(fn (Get $get) => $get("effectDurationType") !== "fixed" || $get("effectDurationType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Valor"),

                            Select::make("elementValue")
                                ->hidden(fn (Get $get) => ($get("effectDurationType") !== "element" && $get("effectDurationType") !== "elementRoll") || $get("effectDurationType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Elemento"),

                            TextInput::make("modifierAddDuration")
                                ->numeric()
                                ->default(0)
                                ->label("Modificador final (Aditivo)"),

                            TextInput::make("modifierMultiplyDuration")
                                ->numeric()
                                ->default(1)
                                ->label("Modificador final (Multiplicativo)"),

                        ])
                        ->label("Efeito"),

                    Block::make("damage")
                        ->schema([
                            Select::make("damageType")
                                ->options([
                                    "fixed" => "Fixo",
                                    "checkDiff" => "Diferença de checagem",
                                    "element" => "Elemento",
                                    "elementRoll" => "Elemento (Rolagem)",
                                    "modifier" => "Modificador"
                                ])
                                ->label("Tipo"),

                            TextInput::make("fixedValue")
                                ->numeric()
                                ->hidden(fn (Get $get) => $get("damageType") !== "fixed" || $get("damageType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Valor"),

                            Select::make("elementValue")
                                ->hidden(fn (Get $get) => ($get("damageType") !== "element" && $get("damageType") !== "elementRoll") || $get("damageType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Elemento"),

                            TextInput::make("modifierAdd")
                                ->numeric()
                                ->default(0)
                                ->label("Modificador final (Aditivo)"),

                            TextInput::make("modifierMultiply")
                                ->numeric()
                                ->default(1)
                                ->label("Modificador final (Multiplicativo)")
                        ])
                        ->label("Dano"),

                    Block::make("heal")
                        ->schema([
                            Select::make("healType")
                                ->options([
                                    "fixed" => "Fixo",
                                    "checkDiff" => "Diferença de checagem",
                                    "element" => "Elemento",
                                    "elementRoll" => "Elemento (Rolagem)",
                                    "modifier" => "Modificador"
                                ])
                                ->label("Tipo"),

                            TextInput::make("fixedValue")
                                ->numeric()
                                ->hidden(fn (Get $get) => $get("healType") !== "fixed" || $get("healType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Valor"),

                            Select::make("elementValue")
                                ->hidden(fn (Get $get) => ($get("healType") !== "element" && $get("healType") !== "elementRoll") || $get("healType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Elemento"),

                            TextInput::make("modifierAdd")
                                ->numeric()
                                ->default(0)
                                ->label("Modificador final (Aditivo)"),

                            TextInput::make("modifierMultiply")
                                ->numeric()
                                ->default(1)
                                ->label("Modificador final (Multiplicativo)")
                        ])
                        ->label("Cura"),

                    Block::make("stat")
                        ->schema([
                            Select::make("stat")
                                ->options([
                                    self::getStats()
                                ])
                                ->required()
                                ->label("Estatística")
                        ])
                        ->label("Modificador de estatística"),

                    Block::make("skill")
                        ->schema([
                            Select::make("skill")
                                ->options([
                                    self::getSkills()
                                ])
                                ->required()
                                ->label("Habilidade")
                        ])
                        ->label("Modificador de habilidade"),

                    Block::make("attribute")
                        ->schema([
                            Select::make("attribute")
                                ->options([
                                   self::getAttributes()
                                ])
                                ->required()
                                ->label("Atributo")
                        ])
                        ->label("Modificador de atributo"),

                    Block::make("check")
                        ->schema([
                            Repeater::make("checkAmount")
                                ->schema([
                                    Select::make("amountType")
                                        ->options([
                                            "fixed" => "Fixo",
                                            "target" => "Alvo",
                                            "user" => "Usuário"
                                        ])
                                        ->required()
                                        ->reactive()
                                        ->label("Quantidade de dados"),

                                    TextInput::make("fixedAmount")
                                        ->numeric()
                                        ->hidden(fn (Get $get) => $get("amountType") !== "fixed" || $get("amountType") === null)
                                        ->required(fn (Component $component) => !$component->isHidden())
                                        ->label("Quantidade de dados (Fixo)"),

                                    Select::make("targetAmount")
                                        ->hidden(fn (Get $get) => $get("amountType") !== "target" || $get("amountType") === null)
                                        ->required(fn (Component $component) => !$component->isHidden())
                                        ->options([
                                            array_merge(self::getSkills(), self::getStats())
                                        ])
                                        ->label("Alvo"),

                                    Select::make("userAmount")
                                        ->hidden(fn (Get $get) => $get("amountType") !== "user" || $get("amountType") === null)
                                        ->required(fn (Component $component) => !$component->isHidden())
                                        ->options([
                                            array_merge(self::getSkills(), self::getStats())
                                        ])
                                        ->label("Usuário"),
                                ])
                                ->label("Quantidade de dados"),

                            Repeater::make("checkTarget")
                                ->schema([
                                    Select::make("targetType")
                                ->options([
                                    "fixed" => "Fixo",
                                    "target" => "Alvo",
                                    "user" => "Usuário",
                                    "difficulty" => "Dificuldade"
                                ])
                                ->required()
                                ->reactive()
                                ->label("Alvo da checagem"),

                            TextInput::make("fixedTarget")
                                ->numeric()
                                ->hidden(fn (Get $get) => $get("targetType") !== "fixed" || $get("targetType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->label("Quantidade de dados (Fixo)"),

                            Select::make("targetTarget")
                                ->hidden(fn (Get $get) => $get("targetType") !== "target" || $get("targetType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->options([
                                    array_merge(self::getSkills(), self::getStats())
                                ])
                                ->label("Alvo"),

                            Select::make("userTarget")
                                ->hidden(fn (Get $get) => $get("targetType") !== "user" || $get("targetType") === null)
                                ->required(fn (Component $component) => !$component->isHidden())
                                ->options([
                                    array_merge(self::getSkills(), self::getStats())
                                ])
                                ->label("Usuário")
                            ])
                            ->label("Alvos de rolagem")
                        ])
                        ->label("Checagem")
                ])
                ->label("Tática")
            ])
            ->label("Estratégia");
    }

    private static function getStats() : array {
        return [
            "strength" => "Força",
            "dexterity" => "Destreza",
            "agility" => "Agilidade",
            "endurance" => "Resistência",
            "charisma" => "Carisma",
            "perception" => "Percepção",
            "intelligence" => "Inteligência",
            "magic" => "Magia",
            "tech" => "Tech",
            "lineage" => "Linhagem",
            "blood" => "Sangue",
            "faith" => "Fé"
        ];
    }

    private static function getSkills() : array {
        return [
            "speed" => "Velocidade",
            "acrobatics" => "Acrobacia",
            "melee" => "Combate corpo a corpo",
            "intimidation" => "Intimidação",
            "ranged" => "Combate à distância",
            "stealth" => "Furtividade",
            "conscience" => "Consciência",
            "investigation" => "Investigação",
            "wisdom" => "Sabedoria",
            "knowledge" => "Conhecimento",
            "medicine" => "Medicina",
            "survival" => "Sobrevivência",
            "tenacity" => "Tenacidade",
            "diplomacy" => "Diplomacia",
            "insight" => "Perspicácia"
        ];
    }

    private static function getAttributes() : array {
        return [
            "health" => "Vida",
            "initiative" => "Iniciativa",
            "movement" => "Movimento",
            "mana" => "Mana",
            "blood_points" => "Sangue",
            "blood_xp_animal" => "Bxp (Animal)",
            "blood_xp_human" => "Bxp (Humano)",
            "blood_xp_vampire" => "Bxp (Vampiro)",
            "circuits" => "Circuitos"
        ];
    }
}
