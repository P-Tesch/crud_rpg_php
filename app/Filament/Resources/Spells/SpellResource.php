<?php

namespace App\Filament\Resources\Spells;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use App\Filament\Resources\Spells\RelationManagers\SchoolsRelationManager;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\Spell;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\Spells\Pages;

class SpellResource extends Resource
{
    protected static ?string $model = Spell::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $modelLabel = 'magia';

    protected static ?string $pluralModelLabel = 'magias';

    protected static string|UnitEnum|null $navigationGroup = 'Magos';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make("name")
                    ->required()
                    ->label("Nome"),

                Textarea::make("description")
                    ->required()
                    ->label("Descrição"),

                Select::make("type")
                    ->options([
                        "PROJECTILE" => "Projétil",
                        "DIRECT" => "Direto"
                    ])
                    ->label("Tipo"),

                TextInput::make("strategy")
                        ->label("Estratégia")
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
                    ->default("OTHER")
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        "OTHER" => "Outro",
                        "PROJECTILE" => "Projétil",
                        "DIRECT" => "Direto",
                        default => ""
                    })
                    ->color(fn (string $state) => match ($state) {
                        "OTHER" => "gray",
                        "PROJECTILE" => "orange",
                        "DIRECT" => "cyan",
                        default => null
                    })
                    ->badge()
                    ->alignCenter()
                    ->label("Tipo"),

                TextColumn::make("schools_max_name")
                    ->max("schools", "name")
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->alignCenter()
                    ->badge()
                    ->label("Escola"),

                TextColumn::make("schools_min_level")
                    ->min("schools", "level")
                    ->sortable()
                    ->alignCenter()
                    ->label("Level mínimo")
            ])
            ->recordActions([
                EditAction::make()
					->hidden(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            SchoolsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpells::route('/'),
            'edit' => Pages\EditSpell::route('/{record}/edit'),
        ];
    }
}
