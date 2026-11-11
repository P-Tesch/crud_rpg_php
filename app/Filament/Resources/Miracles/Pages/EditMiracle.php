<?php

namespace App\Filament\Resources\Miracles\Pages;

use App\Filament\Resources\Miracles\MiracleResource;
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
