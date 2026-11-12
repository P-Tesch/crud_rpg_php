<?php

namespace App\Filament\Resources\SonataAbilities;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use App\Models\SonataAbility;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SonataAbilityResource extends Resource
{
    protected static ?string $model = SonataAbility::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $modelLabel = 'habilidade de sonata';

    protected static ?string $pluralModelLabel = 'habilidades de sontas';

    protected static string|UnitEnum|null $navigationGroup = 'Vampiros';

    protected static ?int $navigationSort = 2;

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
                ->label("Custo"),

                Select::make("sonata_id")
                    ->relationship("sonata", "name")
                    ->preload()
                    ->required()
                    ->label("Sonata"),

                Textarea::make("strategy")
                    ->label("Estratégia"),
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
                    ->alignJustify()
                    ->wrap()
                    ->label("Descrição"),

                TextColumn::make("level")
                    ->sortable()
                    ->alignCenter()
                    ->label("Level"),

                TextColumn::make("cost")
                    ->alignCenter()
                    ->label("Custo"),

                TextColumn::make("sonata.name")
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->label("Sonata"),
            ])
            ->recordActions([
                EditAction::make()
                    ->modal()
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
            'index' => Pages\ListSonataAbilities::route('/'),
            'edit' => Pages\EditSonataAbility::route('/{record}/edit'),
        ];
    }
}
