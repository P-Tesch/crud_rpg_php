<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Sonata;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\SonataResource\Pages;

class SonataResource extends Resource
{
    protected static ?string $model = Sonata::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $navigationLabel = 'Sonatas';

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
                    ->wrap()
                    ->label("Descrição"),

                TextColumn::make("sonataAbilities.name")
                    ->searchable()
                    ->formatStateUsing(
                        fn (string $state) : string =>
                            new HtmlString(str_replace(", ", "<br />", $state))
                    )
                    ->html()
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
            'create' => Pages\CreateSonata::route('/create'),
            'edit' => Pages\EditSonata::route('/{record}/edit'),
        ];
    }
}
