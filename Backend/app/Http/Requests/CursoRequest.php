<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idMateria' => ['required', 'string', 'max:100', 'exists:materias,idMateria'],
            'idDocente' => ['required', 'integer', 'exists:usuarios,idUSuario'],
            'idHorario' => ['required', 'integer', 'exists:horarios,idHorario'],
            'idPeriodo' => ['required', 'integer', 'exists:periodos,idPeriodo'],
            'maxInscritos' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'idMateria.required' => 'La materia es obligatoria.',
            'idMateria.exists' => 'La materia seleccionada no existe.',
            'idDocente.required' => 'El docente es obligatorio.',
            'idDocente.exists' => 'El docente seleccionado no existe.',
            'idHorario.required' => 'El horario es obligatorio.',
            'idHorario.exists' => 'El horario seleccionado no existe.',
            'idPeriodo.required' => 'El regimen academico es obligatorio.',
            'idPeriodo.exists' => 'El regimen academico seleccionado no existe.',
            'maxInscritos.required' => 'El cupo es obligatorio.',
            'maxInscritos.min' => 'El cupo minimo permitido es 1.',
        ];
    }
}
