<?php

namespace App\Filament\Resources\Subsystems;

use App\Filament\Components\StrategyField;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\Subsystem;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;

class SubsystemResource extends Resource
{
    protected static ?string $model = Subsystem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $modelLabel = 'subsistema';

    protected static ?string $pluralModelLabel = 'subsistemas';

    protected static string|UnitEnum|null $navigationGroup = 'Magitecks';

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

                Select::make("system_id")
                    ->relationship("system", "name")
                    ->preload()
                    ->required()
                    ->label("Sistema"),

                StrategyField::make("passiveStrategy", "Estratégia (Passivo)"),

                StrategyField::make("activeStrategy", "Estratégia (Ativo)"),

                StrategyField::make("burnStrategy", "Estratégia (Queimar)"),
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

                TextColumn::make("system.name")
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->badge()
                    ->label("Sistema"),
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
            'index' => Pages\ListSubsystems::route('/'),
            'edit' => Pages\EditSubsystem::route('/{record}/edit'),
        ];
    }
}
