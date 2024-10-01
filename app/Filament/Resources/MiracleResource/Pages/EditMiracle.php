<?php

namespace App\Filament\Resources\MiracleResource\Pages;

use App\Filament\Resources\MiracleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMiracle extends EditRecord
{
    protected static string $resource = MiracleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
