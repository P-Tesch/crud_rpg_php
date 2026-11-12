<?php

namespace App\Filament\Resources\Sheets;

use BackedEnum;
use Filament\Schemas\Schema;
use UnitEnum;
use App\Models\Sheet;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SheetResource extends Resource
{
    protected static ?string $model = Sheet::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $modelLabel = 'Ficha';

    protected static ?string $pluralModelLabel = 'Fichas';

    protected static string|UnitEnum|null $navigationGroup = 'Administrativo';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        fn (string $state): string =>
                            self::formatAlignment($state),
                    )
                    ->label("Alinhamento"),

                TextColumn::make("organization")
                    ->alignCenter()
                    ->default("-")
                    ->formatStateUsing(
                        fn (string $state): string =>
                            self::formatOrganization($state),
                    )
                    ->label("Organização"),
            ])
            ->filters([
                //
            ])
            ->recordActions([
            ])
            ->toolbarActions([
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
            'view' => Pages\ViewSheet::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        fn (string $state): string =>
                            self::formatAlignment($state),
                    )
                    ->label("Alinhamento"),

                TextEntry::make("organization")
                    ->visible(fn ($state) => !is_null($state))
                    ->formatStateUsing(
                        fn (string $state): string =>
                            self::formatOrganization($state),
                    )
                    ->label("Organização"),
            ]);
    }

    private static function formatAlignment(string $state): string
    {
        return match ($state) {
            "fire" => "Fogo",
            "water" => "Água",
            "air" => "Ar",
            "earth" => "Terra",
            "arcana" => "Arcana",
            "void" => "Vazio",
            "ice" => "Gelo",
            "electricity" => "Eletricidade",
            default => "-",
        };
    }

    private static function formatOrganization(string $state): string
    {
        return match ($state) {
            "executors" => "Executores",
            "brotherhood" => "Irmandade",
            "chivalry" => "Cavaleiros",
            "exorcists" => "Exorcistas",
            default => "-",
        };
    }
}
