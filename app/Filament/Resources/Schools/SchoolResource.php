<?php

namespace App\Filament\Resources\Schools;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use App\Filament\Resources\Schools\RelationManagers\SpellsRelationManager;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\School;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $modelLabel = 'escola';

    protected static ?string $pluralModelLabel = 'escolas';

    protected static string|UnitEnum|null $navigationGroup = 'Magos';

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

                TextInput::make("level")
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->label("Level"),

                TextInput::make("cost")
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->label("Custo"),
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

                TextColumn::make("level")
                    ->sortable()
                    ->alignCenter()
                    ->label("Level"),

                TextColumn::make("cost")
                    ->sortable()
                    ->alignCenter()
                    ->label("Custo"),

                TextColumn::make("spells.name")
                    ->searchable()
                    ->alignCenter()
                    ->badge()
                    ->label("Magias"),
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

    public static function getRelations(): array
    {
        return [
            SpellsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
