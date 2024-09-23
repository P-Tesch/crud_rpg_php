<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\School;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\SchoolResource\Pages;
use Illuminate\Support\HtmlString;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Escolas';

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
                    ->label("Descrição"),

                TextColumn::make("level")
                    ->sortable()
                    ->label("Level"),

                TextColumn::make("cost")
                    ->sortable()
                    ->label("Custo"),

                TextColumn::make("spells.name")
                    ->wrap()
                    ->formatStateUsing(
                        fn (string $state) : string =>
                            new HtmlString(str_replace(", ", "<br />", $state))
                    )
                    ->html()
                    ->searchable()
                    ->label("Magias")
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
