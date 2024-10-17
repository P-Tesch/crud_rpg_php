<?php

namespace App\Filament\Resources\SpellResource\Pages;

use App\Models\Strategy;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Actions\CreateAction;
use App\Filament\Resources\SpellResource;
use Filament\Resources\Pages\ListRecords;

class ListSpells extends ListRecords
{
    protected static string $resource = SpellResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data) : array {
                    $formattedTactics = [];

                    foreach ($data["strategy"]["value"] as $tactic) {
                    $formattedTactic = [];

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
                        $formattedTactics[] = $formattedTactic;
                    }

                    return ["tactics" => $formattedTactics];
                })
                ->using(function (array $data): Model {
                    $strategy = new Strategy([
                        "value" => json_encode($data["strategy"]["value"])
                    ]);
                    $strategy->save();
                    $data["strategy_id"] = $strategy->id;

                    return static::getModel()::create($data);
                })
        ];
    }
}
