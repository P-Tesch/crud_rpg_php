<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SheetResource\Pages;
use App\Filament\Resources\SheetResource\RelationManagers;
use App\Models\Sheet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SheetResource extends Resource
{
    protected static ?string $model = Sheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $modelLabel = 'Ficha';

    protected static ?string $pluralModelLabel = 'Fichas';

    protected static ?string $navigationGroup = 'Administrativo';

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

                TextColumn::make("background")
                    ->wrap()
                    ->alignJustify()
                    ->label("História"),

                TextColumn::make("creation_points")
                    ->alignCenter()
                    ->label("Pontos"),

                TextColumn::make("alignment")
                    ->alignCenter()
                    ->default("-")
                    ->formatStateUsing(
                        fn (string $state) : string =>
                            self::formatAlignment($state)
                    )
                    ->label("Alinhamento"),

                TextColumn::make("organization")
                    ->alignCenter()
                    ->default("-")
                    ->formatStateUsing(
                        fn (string $state) : string =>
                            self::formatOrganization($state)
                    )
                    ->label("Organização")
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
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
            'index' => Pages\ListSheets::route('/'),
            'view' => Pages\ViewSheet::route('/{record}')
        ];
    }

    public static function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                TextEntry::make("name")
                    ->label("name"),

                TextEntry::make("description")
                    ->label("Descrição"),

                TextEntry::make("background")
                    ->label("História"),

                TextEntry::make("creation_points")
                    ->label("Pontos de criação"),

                TextEntry::make("alignment")
                    ->visible(fn ($state) => !is_null($state))
                    ->formatStateUsing(
                        fn (string $state) : string =>
                            self::formatAlignment($state)
                    )
                    ->label("Alinhamento"),

                TextEntry::make("organization")
                    ->visible(fn ($state) => !is_null($state))
                    ->formatStateUsing(
                        fn (string $state) : string =>
                            self::formatOrganization($state)
                    )
                    ->label("Organização")
            ]);
    }

    private static function formatAlignment(string $state) : string {
        return match ($state) {
            "fire" => "Fogo",
            "water" => "Água",
            "air" => "Ar",
            "earth" => "Terra",
            "arcana" => "Arcana",
            "void" => "Vazio",
            "ice" => "Gelo",
            "electricity" => "Eletricidade",
            default => "-"
        };
    }

    private static function formatOrganization(string $state) : string {
        return match ($state) {
            "executors" => "Executores",
            "brotherhood" => "Irmandade",
            "chivalry" => "Cavaleiros",
            "exorcists" => "Exorcistas",
            default => "-"
        };
    }
}
