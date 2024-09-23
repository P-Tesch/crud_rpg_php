<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ScriptureAbility;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ScriptureAbilityResource\Pages;
use App\Filament\Resources\ScriptureAbilityResource\RelationManagers;

class ScriptureAbilityResource extends Resource
{
    protected static ?string $model = ScriptureAbility::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Habilidades de escritura';

    protected static ?string $navigationParentItem = 'Escrituras';

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
