<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use App\Models\Sonata;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\SonataResource\Pages;

class SonataResource extends Resource
{
    protected static ?string $model = Sonata::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $modelLabel = 'sonata';

    protected static ?string $pluralModelLabel = 'sonatas';

    protected static ?string $navigationGroup = 'Vampiros';

    protected static ?int $navigationSort = 1;

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
                    ->wrap()
                    ->alignJustify()
                    ->wrap()
                    ->label("Descrição"),

                TextColumn::make("sonataAbilities.name")
                    ->searchable()
                    ->badge()
                    ->alignCenter()
                    ->label("Abilidades")
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
            'index' => Pages\ListSonatas::route('/'),
            'edit' => Pages\EditSonata::route('/{record}/edit'),
        ];
    }
}
