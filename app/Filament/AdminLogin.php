<?php

namespace App\Filament;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login;

class AdminLogin extends Login {

    public function form(Form $form) : Form {
        return $form
            ->schema([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent()
            ]
        );
    }

    protected function getCredentialsFromFormData(array $data) : array {
        return [
            "login" => $data["login"],
            "password" => $data["password"]
        ];
    }

    private function getLoginFormComponent() : Component {
        return TextInput::make("login")
                ->label("Login")
                ->required();
    }
}
