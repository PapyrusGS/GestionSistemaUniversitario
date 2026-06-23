<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarNotaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nota' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors (in Spanish).
     */
    public function messages(): array
    {
        return [
            'nota.required' => 'La nota es obligatoria.',
            'nota.numeric'  => 'La nota debe ser un valor numérico.',
            'nota.min'      => 'La nota debe ser como mínimo 0.',
            'nota.max'      => 'La nota debe ser como máximo 100.',
        ];
    }
}
