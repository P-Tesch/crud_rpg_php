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
