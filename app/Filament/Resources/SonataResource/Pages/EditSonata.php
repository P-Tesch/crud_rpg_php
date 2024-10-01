<?php

namespace App\Filament\Resources\SonataResource\Pages;

use App\Filament\Resources\SonataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSonata extends EditRecord
{
    protected static string $resource = SonataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
