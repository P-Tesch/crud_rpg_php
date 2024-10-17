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

    protected function mutateFormDataBeforeSave(array $data): array {
        $formattedTactics = [];

        foreach ($data["strategy"]["value"] as $tactics) {
            $formattedTactic = [];

            foreach ($tactics as $tactic) {

                foreach ($tactic as $block) {

                    switch ($block["type"]) {
                        case "target":
                            $formattedTactic["target"] = $block["data"]["type"];
                            break;
                        case "check":
                            $formattedTactic["check"] = [
                                "checkAmount" => $block["data"]["checkAmount"],
                                "checkTarget" => $block["data"]["checkTarget"],
                            ];
                            break;
                        case "damage":
                            $formattedTactic["damage"] = $block["data"];
                            break;
                        case "effect":
                            $formattedTactic["effect"] = $block["data"];
                    }
                }
            }
            $formattedTactics[] = $formattedTactic;
        }

        return [
            "strategy" => [
                "value" => [
                    "tactics" => $formattedTactics
                ]
            ]
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model {
        /** @var \App\Models\Spell $record */
        if (isset($record->strategy_id)) {
            Strategy::findOrFail($record->strategy_id)->update([
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
