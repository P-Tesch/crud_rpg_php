<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MiracleResource\Pages;
use App\Models\Miracle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MiracleResource extends Resource
{
    protected static ?string $model = Miracle::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';

    protected static ?string $modelLabel = 'milagre';

    protected static ?string $pluralModelLabel = 'milagres';

    protected static ?string $navigationGroup = 'Clérigos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make("name")
				    ->alphaNum()
				    ->label("Nome"),

                Textarea::make("description")
				    ->label("Descrição"),

                TextInput::make("cost")
				    ->label("Custo")
				    ->numeric(),

                Textarea::make("strategy")
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
            'index' => Pages\ListMiracles::route('/'),
            'create' => Pages\CreateMiracle::route('/create'),
            'edit' => Pages\EditMiracle::route('/{record}/edit'),
        ];
    }
}
