<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EffectResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\EffectResource\RelationManagers\SheetsRelationManager;
use App\Filament\Resources\EffectResource\RelationManagers\SystemsRelationManager;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use App\Models\Effect;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\EffectResource\Pages;

class EffectResource extends Resource
{
    protected static ?string $model = Effect::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $modelLabel = 'efeito';

    protected static ?string $pluralModelLabel = 'efeitos';

    protected static ?string $navigationGroup = 'Geral';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
