<?php

namespace App\Filament\Resources\ScriptureAbilityResource\Pages;

use App\Filament\Resources\ScriptureAbilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScriptureAbilities extends ListRecords
{
    protected static string $resource = ScriptureAbilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
