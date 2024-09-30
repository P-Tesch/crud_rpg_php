<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
use Filament\Tables;
use App\Models\System;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\SystemResource\Pages;

class SystemResource extends Resource
{
    protected static ?string $model = System::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $modelLabel = 'sistema';

    protected static ?string $pluralModelLabel = 'sistemas';

    protected static ?string $navigationGroup = 'Magitecks';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

                TextArea::make("strategy_passive")
                    ->label("Estratégia (Passivo)"),

                TextArea::make("strategy_active")
                    ->label("Estratégia (Ativo)"),

                TextArea::make("strategy_burn")
                    ->label("Estratégia (Queimar)")
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
                        str_contains($state, "(A)") => Color::Yellow,
                        str_contains($state, "(B)") => Color::Red,
                        default => Color::Green
                    })
                    ->label("Subsistemas")

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
