<?php

namespace App\Filament\Resources\ScriptureAbilityResource\Pages;

use App\Filament\Resources\ScriptureAbilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScriptureAbility extends EditRecord
{
    protected static string $resource = ScriptureAbilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
