<?php

namespace App\Filament\Resources;

use App\Filament\Components\StrategyBuilder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\MysticEye;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\MysticEyeResource\Pages;

class MysticEyeResource extends Resource
{
    protected static ?string $model = MysticEye::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $modelLabel = 'olhos místicos';

    protected static ?string $pluralModelLabel = 'olhos místicos';

    protected static ?string $navigationGroup = 'Geral';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

                StrategyBuilder::make("passive_strategy", "Passivo"),

                StrategyBuilder::make("active_strategy", "Ativo")
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
					->label("Custo")
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
            'index' => Pages\ListMysticEyes::route('/'),
            'edit' => Pages\EditMysticEye::route('/{record}/edit'),
        ];
    }
}
