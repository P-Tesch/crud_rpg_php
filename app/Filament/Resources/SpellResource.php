<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpellResource\RelationManagers\SchoolsRelationManager;
use App\Filament\Components\StrategyBuilder;
use App\Models\Strategy;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
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

                StrategyBuilder::make("strategy", "Estratégia")
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
                        "OTHER" => Color::Gray,
                        "PROJECTILE" => Color::Orange,
                        "DIRECT" => Color::Cyan,
                        default => ""
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
