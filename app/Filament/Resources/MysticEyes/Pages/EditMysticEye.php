<?php

namespace App\Filament\Resources\MysticEyes\Pages;

use App\Filament\Resources\MysticEyes\MysticEyeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMysticEye extends EditRecord
{
    protected static string $resource = MysticEyeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
