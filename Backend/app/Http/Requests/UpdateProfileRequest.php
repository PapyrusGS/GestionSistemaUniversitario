<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'telefono' => ['nullable', 'string', 'regex:/^[67]\d{7}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'telefono.regex' => 'El teléfono debe empezar con 6 o 7 y tener exactamente 8 dígitos.',
        ];
    }
}
