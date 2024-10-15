<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpellResource\RelationManagers\SchoolsRelationManager;
use App\Models\Effect;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
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

                Repeater::make("strategy")
                    ->schema([
                        Builder::make("tactic")
                        ->blocks([
                            Block::make("target")
                                ->schema([
                                    Select::make("type")
                                        ->options([
                                            "self" => "Usuário",
                                            "single" => "Único",
                                            "multiple" => "AOE"
                                        ])
                                        ->required()
                                        ->label("Tipo de alvo")
                                ])
                                ->label("Alvo"),

                            Block::make("effect")
                                ->schema([
                                    Select::make("effect")
                                        ->options(Effect::all()->pluck("name", "id"))
                                        ->preload()
                                        ->searchable()
                                        ->required(true)
                                        ->label("Efeito"),

                                    TextInput::make("duration")
                                        ->numeric()
                                        ->label("Duração")

                                ])
                                ->label("Efeito"),

                            Block::make("damage")
                                ->schema([
                                    TextInput::make("value")
                                        ->numeric()
                                        ->required()
                                        ->label("Valor")
                                ])
                                ->label("Dano"),

                            Block::make("heal")
                                ->schema([
                                    TextInput::make("value")
                                        ->numeric()
                                        ->required()
                                        ->label("valor")
                                ])
                                ->label("Cura"),

                            Block::make("stat")
                                ->schema([
                                    Select::make("stat")
                                        ->options([
                                            "strength" => "Força",
                                            "dexterity" => "Destreza",
                                            "agility" => "Agilidade",
                                            "endurance" => "Resistência",
                                            "charisma" => "Carisma",
                                            "perception" => "Percepção",
                                            "intelligence" => "Inteligência",
                                            "magic" => "Magia",
                                            "tech" => "Tech",
                                            "lineage" => "Linhagem",
                                            "blood" => "Sangue",
                                            "faith" => "Fé"
                                        ])
                                        ->required()
                                        ->label("Estatística")
                                ])
                                ->label("Modificador de estatística"),

                            Block::make("skill")
                                ->schema([
                                    Select::make("skill")
                                        ->options([
                                            "speed" => "Velocidade",
                                            "acrobatics" => "Acrobacia",
                                            "melee" => "Combate corpo a corpo",
                                            "intimidation" => "Intimidação",
                                            "ranged" => "Combate à distância",
                                            "stealth" => "Furtividade",
                                            "conscience" => "Consciência",
                                            "investigation" => "Investigação",
                                            "wisdom" => "Sabedoria",
                                            "knowledge" => "Conhecimento",
                                            "medicine" => "Medicina",
                                            "survival" => "Sobrevivência",
                                            "tenacity" => "Tenacidade",
                                            "diplomacy" => "Diplomacia",
                                            "insight" => "Perspicácia"
                                        ])
                                        ->required()
                                        ->label("Habilidade")
                                ])
                                ->label("Modificador de habilidade"),

                            Block::make("attribute")
                                ->schema([
                                    Select::make("attribute")
                                        ->options([
                                            "health" => "Vida",
                                            "initiative" => "Iniciativa",
                                            "movement" => "Movimento",
                                            "mana" => "Mana",
                                            "blood_points" => "Sangue",
                                            "blood_xp_animal" => "Bxp (Animal)",
                                            "blood_xp_human" => "Bxp (Humano)",
                                            "blood_xp_vampire" => "Bxp (Vampiro)",
                                            "circuits" => "Circuitos"
                                        ])
                                        ->required()
                                        ->label("Atributo")
                                ])
                        ])
                        ->label("Tática")
                    ])
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
