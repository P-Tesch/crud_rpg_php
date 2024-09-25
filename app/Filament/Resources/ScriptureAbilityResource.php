<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ScriptureAbility;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\ScriptureAbilityResource\Pages;

class ScriptureAbilityResource extends Resource
{
    protected static ?string $model = ScriptureAbility::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $modelLabel = 'habilidade de escritura';

    protected static ?string $pluralModelLabel = 'habilidades de escritura';

    protected static ?string $navigationGroup = 'Clérigos';

    protected static ?int $navigationSort = 3;

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
            'create' => Pages\CreateScriptureAbility::route('/create'),
            'edit' => Pages\EditScriptureAbility::route('/{record}/edit'),
        ];
    }
}
