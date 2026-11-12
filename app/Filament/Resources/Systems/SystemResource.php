<?php

namespace App\Filament\Resources\Systems;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\System;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;

class SystemResource extends Resource
{
    protected static ?string $model = System::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $modelLabel = 'sistema';

    protected static ?string $pluralModelLabel = 'sistemas';

    protected static string|UnitEnum|null $navigationGroup = 'Magitecks';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make("name")
                    ->required()
                    ->label("Nome"),

                Textarea::make("description")
                    ->required()
                    ->label("Descrição"),

                TextInput::make("damage")
                    ->required()
                    ->numeric()
                    ->label("Dano"),

                Textarea::make("strategy_passive")
                    ->label("Estratégia (Passivo)"),

                Textarea::make("strategy_active")
                    ->label("Estratégia (Ativo)"),

                Textarea::make("strategy_burn")
                    ->label("Estratégia (Queimar)"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")
                    ->searchable()
                    ->label("Nome"),

                TextColumn::make("description")
                    ->wrap()
                    ->alignJustify()
                    ->label("Descrição"),

                TextColumn::make("damage")
                    ->sortable()
                    ->alignCenter()
                    ->placeholder("-")
                    ->label("Dano"),

                TextColumn::make("subsystems.name")
                    ->searchable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        str_contains($state, "(A)") => "yellow",
                        str_contains($state, "(B)") => "red",
                        default => "green",
                    })
                    ->label("Subsistemas"),

            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystems::route('/'),
            'edit' => Pages\EditSystem::route('/{record}/edit'),
        ];
    }
}
