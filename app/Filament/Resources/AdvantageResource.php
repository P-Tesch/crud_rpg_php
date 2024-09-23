<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Advantage;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\AdvantageResource\Pages;

class AdvantageResource extends Resource
{
    protected static ?string $model = Advantage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chevron-double-up';

    protected static ?string $navigationLabel = 'Vantagens';

    protected ?string $heading = 'Vantagens';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make("name")
					->alphaNum()
					->label("Nome")
					->maxLength(63),

                Textarea::make("description")
					->alphaNum()
					->label("Descrição")
					->maxLength(511)
					->rows(5),

                TextInput::make("level")
					->label("Level")
					->numeric()
					->minValue(0),

                TextInput::make("cost")
					->label("Custo")
					->numeric(),

                Select::make("class")
					->label("Classe")
					->options([
                        "null" => "Todos",
                        "mage" => "Mago",
                        "cleric" => "Clérigo",
                        "vampire" => "Vampiro",
                        "hybrid" => "Híbrido",
                        "magiteck" => "Magiteck"
                    ]),

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
                    ->wrap()
					->label("Descrição"),

                TextColumn::make("level")
					->sortable()
                    ->alignCenter()
					->label("Level"),

                TextColumn::make("cost")
					->sortable()
                    ->alignCenter()
					->label("Custo"),

                TextColumn::make("class")
					->sortable()
                    ->alignCenter()
                    ->formatStateUsing(
                        fn (string $state) : string =>
                            self::formatClassName($state)
                    )
					->label("Classe")
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
            'index' => Pages\ListAdvantages::route('/'),
            'create' => Pages\CreateAdvantage::route('/create'),
            'edit' => Pages\EditAdvantage::route('/{record}/edit'),
        ];
    }

    private static function formatClassName(string $className) : string {
        return match ($className) {
            "mage" => "Mago",
            "cleric" => "Clérigo",
            "vampire" => "Vampiro",
            "hybrid" => "Híbrido",
            "magiteck" => "Magiteck"
        };
    }
}
