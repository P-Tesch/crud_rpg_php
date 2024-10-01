<?php

namespace App\Filament\Resources\ScriptureResource\Pages;

use App\Filament\Resources\ScriptureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScriptures extends ListRecords
{
    protected static string $resource = ScriptureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
