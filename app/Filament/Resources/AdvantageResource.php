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

    protected static ?string $modelLabel = 'vantagem';

    protected static ?string $pluralModelLabel = 'vantagens';

    protected static ?string $navigationGroup = 'Geral';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make("name")
					->alphaNum()
					->maxLength(63)
                    ->required()
					->label("Nome"),

                Textarea::make("description")
					->alphaNum()
					->maxLength(511)
					->rows(5)
                    ->required()
					->label("Descrição"),

                TextInput::make("level")
                    ->numeric()
                    ->minValue(0)
                    ->label("Level"),

                TextInput::make("cost")
                    ->numeric()
                    ->required()
                    ->label("Custo"),

                Select::make("class")
                    ->options([
                        "mage" => "Mago",
                        "cleric" => "Clérigo",
                        "vampire" => "Vampiro",
                        "hybrid" => "Híbrido",
                        "magiteck" => "Magiteck"
                    ])
                    ->label("Classe"),

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
            "magiteck" => "Magiteck",
            default => ""
        };
    }
}
