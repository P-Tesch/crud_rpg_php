<?php

namespace App\Filament\Resources\ScriptureAbilities;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use App\Models\ScriptureAbility;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\ScriptureAbilities\Pages;

class ScriptureAbilityResource extends Resource
{
    protected static ?string $model = ScriptureAbility::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $modelLabel = 'habilidade de escritura';

    protected static ?string $pluralModelLabel = 'habilidades de escritura';

    protected static string|UnitEnum|null $navigationGroup = 'Clérigos';

    protected static ?int $navigationSort = 3;

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
                    ->numeric()
                    ->minValue(0)
                    ->label("Level"),

                TextInput::make("cost")
                    ->numeric()
                    ->label("Custo"),

                Textarea::make("strategy")
                    ->label("Estratégia")
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
                    ->alignCenter()
                    ->sortable()
                    ->label("Level"),

                TextColumn::make("cost")
                    ->sortable()
                    ->alignCenter()
                    ->label("Custo"),

                TextColumn::make("scripture_count")
                    ->counts("scripture")
                    ->alignCenter()
                    ->sortable()
                    ->label("Qtd. Escrituras")

            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScriptureAbilities::route('/'),
            'edit' => Pages\EditScriptureAbility::route('/{record}/edit'),
        ];
    }
}
