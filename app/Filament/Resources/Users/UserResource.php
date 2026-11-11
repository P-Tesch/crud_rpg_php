<?php

namespace App\Filament\Resources\Users;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use UnitEnum;
use App\Models\User;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\Users\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $modelLabel = 'usuário';

    protected static ?string $pluralModelLabel = 'usuários';

    protected static string|UnitEnum|null $navigationGroup = 'Administrativo';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
