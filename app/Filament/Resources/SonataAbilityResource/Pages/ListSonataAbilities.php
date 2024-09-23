<?php

namespace App\Filament\Resources\SonataAbilityResource\Pages;

use App\Filament\Resources\SonataAbilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSonataAbilities extends ListRecords
{
    protected static string $resource = SonataAbilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
