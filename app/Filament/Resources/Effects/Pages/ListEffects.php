<?php

namespace App\Filament\Resources\Effects\Pages;

use App\Filament\Resources\Effects\EffectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEffects extends ListRecords
{
    protected static string $resource = EffectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
