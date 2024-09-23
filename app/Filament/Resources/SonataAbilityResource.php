<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SonataAbilityResource\Pages;
use App\Filament\Resources\SonataAbilityResource\RelationManagers;
use App\Models\SonataAbility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SonataAbilityResource extends Resource
{
    protected static ?string $model = SonataAbility::class;

    protected static ?string $navigationLabel = 'Habilidades de sonata';

    protected static ?string $navigationParentItem = 'Sonatas';

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

                TextColumn::make("level")
                    ->sortable()
                    ->alignCenter()
                    ->label("Level"),

                TextColumn::make("sonata.name")
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->label("Sonata")
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
            'index' => Pages\ListSonataAbilities::route('/'),
            'create' => Pages\CreateSonataAbility::route('/create'),
            'edit' => Pages\EditSonataAbility::route('/{record}/edit'),
        ];
    }
}
