<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignarDocenteRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idDocente' => ['required', 'integer', 'exists:usuarios,idUsuario'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'idDocente.required' => 'El docente es obligatorio.',
            'idDocente.exists' => 'El docente seleccionado no es válido.',
        ];
    }
}
