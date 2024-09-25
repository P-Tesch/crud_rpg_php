<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Spell;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\SpellResource\Pages;

class SpellResource extends Resource
{
    protected static ?string $model = Spell::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $modelLabel = 'magia';

    protected static ?string $pluralModelLabel = 'magias';

    protected static ?string $navigationGroup = 'Magos';

    protected static ?int $navigationSort = 2;

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

                TextColumn::make("type")
                    ->sortable()
                    ->placeholder("Outro")
                    ->alignCenter()
                    ->label("Tipo"),

                TextColumn::make("school_max_name")
                    ->max("school", "name")
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->alignCenter()
                    ->label("Escola"),

                TextColumn::make("school_min_level")
                    ->min("school", "level")
                    ->sortable()
                    ->alignCenter()
                    ->label("Level mínimo")
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpells::route('/'),
            'create' => Pages\CreateSpell::route('/create'),
            'edit' => Pages\EditSpell::route('/{record}/edit'),
        ];
    }
}
