<?php

namespace App\Filament;

use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class AdminLogin extends Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ],
            );
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            "login" => $data["login"],
            "password" => $data["password"],
        ];
    }

    private function getLoginFormComponent(): Component
    {
        return TextInput::make("login")
                ->label("Login")
                ->required();
    }
}
