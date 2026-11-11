<?php

namespace App\Filament\Resources\Miracles\Pages;

use App\Filament\Resources\Miracles\MiracleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMiracles extends ListRecords
{
    protected static string $resource = MiracleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
