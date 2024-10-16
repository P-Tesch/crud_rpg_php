<?php

namespace App\Filament\Resources\SpellResource\Pages;

use Filament\Actions;
use App\Models\Strategy;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SpellResource;

class EditSpell extends EditRecord
{
    protected static string $resource = SpellResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    protected function handleRecordUpdate(Model $record, array $data): Model {
        if (isset($record->strategy_id)) {
            Strategy::find($record->strategy_id)->update([
                "value" => json_encode($data["strategy"]["value"])
            ]);
        } else {
            $strategy = new Strategy([
                "value" => json_encode($data["strategy"]["value"])
            ]);
            $strategy->save();

            $data["strategy_id"] = $strategy->id;
        }

        $record->update($data);

        return $record;
    }
}
