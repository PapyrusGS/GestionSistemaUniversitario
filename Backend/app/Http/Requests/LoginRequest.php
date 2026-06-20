<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'El correo o CI es obligatorio.',
            'login.string' => 'El correo o CI debe ser una cadena de texto.',
            'password.required' => 'La contrasena es obligatoria.',
            'password.string' => 'La contrasena debe ser una cadena de texto.',
        ];
    }
}
