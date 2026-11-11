<?php

namespace App\Filament\Resources\MysticEyes\Pages;

use App\Filament\Resources\MysticEyes\MysticEyeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMysticEyes extends ListRecords
{
    protected static string $resource = MysticEyeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
