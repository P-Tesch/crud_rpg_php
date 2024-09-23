<?php

namespace App\Filament\Resources;

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

    protected static ?string $navigationLabel = 'Olhos místicos';

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
                    ->placeholder("0")
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
            'create' => Pages\CreateMysticEye::route('/create'),
            'edit' => Pages\EditMysticEye::route('/{record}/edit'),
        ];
    }
}
