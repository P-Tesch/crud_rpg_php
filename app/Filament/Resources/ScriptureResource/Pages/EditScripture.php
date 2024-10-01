<?php

namespace App\Filament\Resources\ScriptureResource\Pages;

use App\Filament\Resources\ScriptureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScripture extends EditRecord
{
    protected static string $resource = ScriptureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
