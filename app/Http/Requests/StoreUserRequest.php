<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest {

    /** @return array<string, array<int, mixed>> */
    public function rules() : array {
        return [
            "login" => [
                "required",
                "unique:users,login",
                "min: 4",
                "max: 31",
                "alpha_num: ascii"

            ],
            "password" => [
                "required",
                Password::min(6)->max(31)
            ]
        ];
    }

    /** @return array<string, string> */
    public function messages() : array {
        return [
            "required" => "Campo obrigatório",
            "unique" => "Nome de usuário em uso",
            "login.min" => "Mínimo de 4 caracteres",
            "login.max" => "Máximo de 31 caracteres",
            "alpha_num" => "Apenas letras e números",
            "password.min" => "Mínimo de 6 caracteres",
            "password.max" => "Máximo de 31 caracteres"
        ];
    }
}
