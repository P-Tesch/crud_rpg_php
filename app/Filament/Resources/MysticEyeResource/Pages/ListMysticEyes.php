<?php

namespace App\Filament\Resources\MysticEyeResource\Pages;

use Filament\Actions;
use App\Models\Strategy;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\MysticEyeResource;

class ListMysticEyes extends ListRecords
{
    protected static string $resource = MysticEyeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data) : array {
                    $formattedPassiveTactics = [];
                    foreach ($data["passive_strategy"]["value"] as $tactics) {
                        $formattedPassiveTactics[] = $this->formatTactic($tactics);
                    }

                    $formattedActiveTactics = [];
                    foreach ($data["active_strategy"]["value"] as $tactics) {
                        $formattedActiveTactics[] = $this->formatTactic($tactics);
                    }

                    return [
                        "passive_strategy" => [
                            "value" => [
                                "tactics" => $formattedPassiveTactics
                            ]
                        ],
                        "active_strategy" => [
                            "value" => [
                                "tactics" => $formattedActiveTactics
                            ]
                        ]
                    ];
                })
                ->using(function (array $data): Model {
                    $strategy = new Strategy([
                        "value" => json_encode($data["passive_strategy"]["value"])
                    ]);
                    $strategy->save();
                    $data["passive_strategy_id"] = $strategy->id;

                    $strategy = new Strategy([
                        "value" => json_encode($data["active_strategy"]["value"])
                    ]);
                    $strategy->save();
                    $data["active_strategy_id"] = $strategy->id;

                    return static::getModel()::create($data);
                })
        ];
    }

    private function formatTactic(array $tactics) : array {
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

        return $formattedTactic;
    }
}
