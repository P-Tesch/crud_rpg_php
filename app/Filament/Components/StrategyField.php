<?php

namespace App\Filament\Components;

use App\Rules\ValidLuaStrategy;
use Filament\Schemas\Components\Section;

class StrategyField
{
    public static function make(string $relationship, string $label): Section
    {
        return Section::make($label)
            ->relationship($relationship, condition: fn (?array $rawState): bool => filled($rawState["code"] ?? null))
            ->schema([
                LuaCodeEditor::make("code")
                    ->hiddenLabel()
                    ->default("-- Utilize as funções rpg.* para executar ações\nlocal ctx = ... -- Não remover, use para acessar os parâmetros de contexto")
                    ->rules([new ValidLuaStrategy()]),
            ])
            ->collapsible()
            ->collapsed(fn (?array $rawState): bool => blank($rawState["code"] ?? null))
            ->columnSpanFull();
    }
}
