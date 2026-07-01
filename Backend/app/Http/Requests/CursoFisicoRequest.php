<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CursoFisicoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('idCurso')) {
            $this->merge([
                'idCurso' => str_replace(' ', '', strtoupper($this->input('idCurso'))),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $idCurso = $this->route('idCurso');

        return [
            'idCurso' => [
                'required',
                'string',
                'max:20',
                'regex:/^(?:CUR|LAB)-[A-Za-z0-9]+$/',
                Rule::unique('cursos', 'idCurso')->ignore($idCurso, 'idCurso'),
            ],
            'capacidad' => [
                'required',
                'integer',
                'min:1',
                'max:40',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors (in Spanish).
     */
    public function messages(): array
    {
        return [
            'idCurso.required' => 'El código del curso es obligatorio.',
            'idCurso.string' => 'El código del curso debe ser una cadena de texto.',
            'idCurso.max' => 'El código del curso no puede superar los 20 caracteres.',
            'idCurso.regex' => 'El código debe empezar con "CUR-" o "LAB-" seguido de letras o números (ej: CUR-101, LAB-201).',
            'idCurso.unique' => 'Ya existe un curso físico con ese código.',
            'capacidad.required' => 'La capacidad del curso es obligatoria.',
            'capacidad.integer' => 'La capacidad del curso debe ser un número entero.',
            'capacidad.min' => 'La capacidad debe ser un número positivo.',
            'capacidad.max' => 'La capacidad máxima permitida es de 40 personas.',
        ];
    }
}
