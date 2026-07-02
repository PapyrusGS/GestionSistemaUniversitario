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

        $rules = [
            'idMateria' => [
                'required',
                'string',
                'max:100',
                Rule::unique('materias', 'idMateria')->ignore($materiaId, 'idMateria'),
            ],
            'idCarrera' => ['required', 'integer', 'exists:carreras,idCarrera'],
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

        $semestre = $this->input('semestre');
        $rules['idMateriaPrevia'] = ['nullable'];

        if ($semestre === '1') {
            $rules['idMateriaPrevia'][] = function ($attribute, $value, $fail) {
                if (!empty($value)) {
                    $fail('Las materias de primer semestre no pueden tener prerrequisito.');
                }
            };
        } else {
            $rules['idMateriaPrevia'][] = 'string';
            $rules['idMateriaPrevia'][] = 'max:100';
            $rules['idMateriaPrevia'][] = 'different:idMateria';
            $rules['idMateriaPrevia'][] = 'exists:materias,idMateria';
            $rules['idMateriaPrevia'][] = function ($attribute, $value, $fail) {
                if (!empty($value)) {
                    $sem = \Illuminate\Support\Facades\DB::table('materias')
                        ->where('idMateria', $value)
                        ->value('semestre');
                    if ($sem && strtolower($sem) === 'electiva') {
                        $fail('Una materia electiva no puede ser prerequisito de otra materia.');
                    }
                }
            };
        }

        return $rules;
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
