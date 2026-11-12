<?php

namespace App\Filament\Resources\Scriptures\Pages;

use App\Filament\Resources\Scriptures\ScriptureResource;
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
