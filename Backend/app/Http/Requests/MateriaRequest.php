<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $materiaId = $this->route('materia');

        return [
            'idMateria' => [
                'required',
                'string',
                'max:100',
                Rule::unique('materias', 'idMateria')->ignore($materiaId, 'idMateria'),
            ],
            'idCarrera' => ['required', 'integer', 'exists:carreras,idCarrera'],
            'idMateriaPrevia' => ['nullable', 'string', 'max:100', 'different:idMateria', 'exists:materias,idMateria'],
            'nombre' => [
                'required',
                'string',
                'min:5',
                'max:30',
                Rule::unique('materias', 'nombre')
                    ->where(fn ($query) => $query->where('idCarrera', $this->input('idCarrera')))
                    ->ignore($materiaId, 'idMateria'),
            ],
            'semestre' => ['required', 'string', 'max:10', 'regex:/^(?:[1-9]|10|Electiva)$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'idMateria.required' => 'El codigo de la materia es obligatorio.',
            'idMateria.unique' => 'Ya existe una materia con ese codigo.',
            'idCarrera.required' => 'La carrera es obligatoria.',
            'idCarrera.exists' => 'La carrera seleccionada no existe.',
            'idMateriaPrevia.different' => 'La materia no puede depender de si misma.',
            'idMateriaPrevia.exists' => 'La materia prerrequisito seleccionada no existe.',
            'nombre.required' => 'El nombre de la materia es obligatorio.',
            'nombre.min' => 'El nombre de la materia debe tener al menos 5 caracteres.',
            'nombre.max' => 'El nombre de la materia no puede superar los 30 caracteres.',
            'nombre.unique' => 'Ya existe una materia con ese nombre en la carrera seleccionada.',
            'semestre.required' => 'El semestre es obligatorio.',
            'semestre.regex' => 'El semestre debe ser un número del 1 al 10 o "Electiva".',
        ];
    }
}
