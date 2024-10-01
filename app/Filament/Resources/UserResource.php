<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $modelLabel = 'usuário';

    protected static ?string $pluralModelLabel = 'usuários';

    protected static ?string $navigationGroup = 'Administrativo';

    protected static ?int $navigationSort = 1;

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
                TextColumn::make("login")
                    ->label("Login"),

                IconColumn::make("sheet_id")
                    ->default(false)
                    ->icon(
                        fn (bool $state) : string =>
                            $state ? "heroicon-o-check-circle": "heroicon-o-x-circle"
                    )
                    ->color(
                        fn (bool $state) : string =>
                            $state ? "success" : "danger"
                    )
                    ->alignCenter()
                    ->label("Jogador"),

                IconColumn::make("is_master")
                    ->icon(
                        fn (bool $state) : string =>
                            $state ? "heroicon-o-check-circle": "heroicon-o-x-circle"
                    )
                    ->color(
                        fn (bool $state) : string =>
                            $state ? "success" : "danger"
                    )
                    ->alignCenter()
                    ->label("Mestre"),

                IconColumn::make("is_admin")
                    ->icon(
                        fn (bool $state) : string =>
                            $state ? "heroicon-o-check-circle": "heroicon-o-x-circle"
                    )
                    ->color(
                        fn (bool $state) : string =>
                            $state ? "success" : "danger"
                    )
                    ->alignCenter()
                    ->label("Admin"),


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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
