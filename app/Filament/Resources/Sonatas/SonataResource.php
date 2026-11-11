<?php

namespace App\Filament\Resources\Sonatas;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\Sonata;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\Sonatas\Pages;

class SonataResource extends Resource
{
    protected static ?string $model = Sonata::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $modelLabel = 'sonata';

    protected static ?string $pluralModelLabel = 'sonatas';

    protected static string|UnitEnum|null $navigationGroup = 'Vampiros';

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
                    ->label("Descrição")
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
                    ->wrap()
                    ->label("Descrição"),

                TextColumn::make("sonataAbilities.name")
                    ->searchable()
                    ->badge()
                    ->alignCenter()
                    ->label("Abilidades")
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
            'index' => Pages\ListSonatas::route('/'),
            'edit' => Pages\EditSonata::route('/{record}/edit'),
        ];
    }
}
