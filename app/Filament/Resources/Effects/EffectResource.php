<?php

namespace App\Filament\Resources\Effects;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use App\Filament\Resources\Effects\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Effects\RelationManagers\SheetsRelationManager;
use App\Filament\Resources\Effects\RelationManagers\SystemsRelationManager;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\Effect;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\Effects\Pages;

class EffectResource extends Resource
{
    protected static ?string $model = Effect::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static ?string $modelLabel = 'efeito';

    protected static ?string $pluralModelLabel = 'efeitos';

    protected static string|UnitEnum|null $navigationGroup = 'Geral';

    protected static ?int $navigationSort = 4;

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
                    ->alignJustify()
                    ->wrap()
                    ->label("Descrição"),

                TextColumn::make("sheets_count")
                    ->counts("sheets")
                    ->alignCenter()
                    ->label("Qtd. Personagens afetados"),

                TextColumn::make("items_count")
                    ->counts("items")
                    ->alignCenter()
                    ->label("Qtd. Itens afetados"),

                TextColumn::make("systems_count")
                    ->counts("systems")
                    ->alignCenter()
                    ->label("Qtd. Sistemas afetados")

            ])
            ->recordActions([
                EditAction::make(),
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
            ItemsRelationManager::class,
            SheetsRelationManager::class,
            SystemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEffects::route('/'),
            'create' => Pages\CreateEffect::route('/create'),
            'edit' => Pages\EditEffect::route('/{record}/edit'),
        ];
    }
}
