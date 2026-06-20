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
                'max:255',
                Rule::unique('materias', 'nombre')
                    ->where(fn ($query) => $query->where('idCarrera', $this->input('idCarrera')))
                    ->ignore($materiaId, 'idMateria'),
            ],
            'semestre' => ['required', 'integer', 'min:1', 'max:20'],
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
            'nombre.unique' => 'Ya existe una materia con ese nombre en la carrera seleccionada.',
            'semestre.required' => 'El semestre es obligatorio.',
            'semestre.integer' => 'El semestre debe ser un numero entero.',
            'semestre.min' => 'El semestre minimo permitido es 1.',
        ];
    }
}
