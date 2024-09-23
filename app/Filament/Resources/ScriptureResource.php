<?php

namespace App\Filament\Resources;

use App\Models\Scripture;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\ScriptureResource\Pages;

class ScriptureResource extends Resource
{
    protected static ?string $model = Scripture::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static ?string $navigationLabel = 'Escrituras';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")
                    ->searchable()
                    ->label("Nome"),

                TextColumn::make("sheet.name")
                    ->alignCenter()
                    ->searchable()
                    ->label("Dono"),

                TextColumn::make("description")
                    ->alignJustify()
                    ->label("Descrição"),

                TextColumn::make("creation_points")
                    ->alignCenter()
                    ->label("Pontos de criação"),

                TextColumn::make("remaining_scripture_points")
                    ->alignCenter()
                    ->label("Pontos de escritura"),

                TextColumn::make("damage")
                    ->alignCenter()
                    ->label("Dano"),

                TextColumn::make("range")
                    ->alignCenter()
                    ->label("Alcance"),

                TextColumn::make("sharpness")
                    ->alignCenter()
                    ->label("Afiação"),

                IconColumn::make("double")
                    ->icon(
                        fn (bool $state) : string =>
                            $state ? "heroicon-o-check-circle": "heroicon-o-x-circle"
                    )
                    ->color(
                        fn (bool $state) : string =>
                            $state ? "success" : "danger"
                    )
                    ->alignCenter()
                    ->label("Duplo"),

                TextColumn::make("scriptureAbilities.name")
                    ->formatStateUsing(
                        fn (string $state) : string =>
                            new HtmlString(str_replace(", ", "<br />", $state))
                    )
                    ->html()
                    ->label("Habilidades")
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScriptures::route('/')
        ];
    }

    public static function canCreate() : bool {
        return false;
    }
}
