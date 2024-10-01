<?php

namespace App\Filament\Resources\SonataResource\Pages;

use App\Filament\Resources\SonataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSonatas extends ListRecords
{
    protected static string $resource = SonataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
