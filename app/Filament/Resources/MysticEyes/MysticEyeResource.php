<?php

namespace App\Filament\Resources\MysticEyes;

use App\Filament\Components\StrategyField;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\MysticEye;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;

class MysticEyeResource extends Resource
{
    protected static ?string $model = MysticEye::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-eye';

    protected static ?string $modelLabel = 'olhos místicos';

    protected static ?string $pluralModelLabel = 'olhos místicos';

    protected static string|UnitEnum|null $navigationGroup = 'Geral';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make("name")
                    ->required()
                    ->label("Nome"),

                Textarea::make("passive")
                    ->label("Passivo"),

                Textarea::make("active")
                    ->label("Ativo"),

                TextInput::make("cooldown")
                    ->numeric()
                    ->minValue(0)
                    ->label("Cooldown"),

                TextInput::make("cost")
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->label("Custo"),

                StrategyField::make("passiveStrategy", "Estratégia (Passivo)"),

                StrategyField::make("activeStrategy", "Estratégia (Ativo)"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")
                    ->searchable()
                    ->label("Nome"),

                TextColumn::make("passive")
                    ->wrap()
                    ->placeholder("Sem efeito passivo")
                    ->alignJustify()
                    ->label("Passivo"),

                TextColumn::make("active")
                    ->wrap()
                    ->placeholder("Sem efeito ativo")
                    ->alignJustify()
                    ->label("Ativo"),

                TextColumn::make("cooldown")
                    ->sortable()
                    ->default("-")
                    ->alignCenter()
                    ->label("Cooldown"),

                TextColumn::make("cost")
                    ->sortable()
                    ->alignCenter()
                    ->label("Custo"),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMysticEyes::route('/'),
            'edit' => Pages\EditMysticEye::route('/{record}/edit'),
        ];
    }
}
