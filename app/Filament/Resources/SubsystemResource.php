<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Subsystem;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\SubsystemResource\Pages;

class SubsystemResource extends Resource
{
    protected static ?string $model = Subsystem::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $modelLabel = 'subsistema';

    protected static ?string $pluralModelLabel = 'subsistemas';

    protected static ?string $navigationGroup = 'Magitecks';

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

                Select::make("system_id")
                    ->relationship("system", "name")
                    ->preload()
                    ->required()
                    ->label("Sistema"),

                TextArea::make("strategy")
                    ->label("Estratégia"),

                TextArea::make("strategy_burn")
                    ->label("Estratégia (Queimar)")
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
                    ->label("Sistema")
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
            'index' => Pages\ListSubsystems::route('/'),
            'edit' => Pages\EditSubsystem::route('/{record}/edit'),
        ];
    }
}
