<?php

namespace App\Filament\Components;

use App\Services\Strategy\StrategyEngine;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class LuaCodeEditor extends Field
{
    use CanWrap, HasExtraAlpineAttributes;

    protected string $view = "filament.forms.components.lua-code-editor";

    /**
     * Completions for the strategy API, as label => description.
     *
     * @return array<string, string>
     */
    public function getApiCompletions(): array
    {
        return app(StrategyEngine::class)->api;
    }
}
