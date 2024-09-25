<?php

namespace App\Filament\Resources;

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
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEffects::route('/'),
            'create' => Pages\CreateEffect::route('/create'),
            'edit' => Pages\EditEffect::route('/{record}/edit'),
        ];
    }
}
