<?php

namespace App\Filament\Resources\Scriptures\Pages;

use App\Filament\Resources\Scriptures\ScriptureResource;
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
