<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarNotaRequest extends FormRequest
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
            'estudiante_materia_id' => [
                'required',
                'integer',
                'exists:estudiantemateria,idInscripcion',
            ],
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
            'estudiante_materia_id.required' => 'El id de la inscripcion (estudiante_materia_id) es obligatorio.',
            'estudiante_materia_id.integer' => 'El id de la inscripcion debe ser un numero entero.',
            'estudiante_materia_id.exists' => 'La inscripcion no existe.',
            'nota.required' => 'La nota es obligatoria.',
            'nota.numeric' => 'La nota debe ser un valor numerico.',
            'nota.min' => 'La nota debe ser como minimo 0.',
            'nota.max' => 'La nota debe ser como maximo 100.',
        ];
    }
}
