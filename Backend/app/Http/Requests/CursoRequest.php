<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'idHorario2' => $this->idHorario2 ? (int) $this->idHorario2 : null,
            'idHorario3' => $this->idHorario3 ? (int) $this->idHorario3 : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'idCurso' => ['required', 'string', 'max:100', 'exists:cursos,idCurso'],
            'idMateria' => ['required', 'string', 'max:100', 'exists:materias,idMateria'],
            'idDocente' => ['required', 'integer', 'exists:usuarios,idUsuario'],
            'idHorario1' => ['required', 'integer', 'exists:horarios,idHorario'],
            'idHorario2' => ['nullable', 'integer', 'exists:horarios,idHorario'],
            'idHorario3' => ['nullable', 'integer', 'exists:horarios,idHorario'],
            'idPeriodo' => ['required', 'integer', 'exists:periodos,idPeriodo']
        ];
    }

    public function messages(): array
    {
        return [
            'idCurso.required' => 'El curso/aula es obligatorio.',
            'idCurso.exists' => 'El curso/aula seleccionado no existe.',
            'idMateria.required' => 'La materia es obligatoria.',
            'idMateria.exists' => 'La materia seleccionada no existe.',
            'idDocente.required' => 'El docente es obligatorio.',
            'idDocente.exists' => 'El docente seleccionado no existe.',
            'idHorario1.required' => 'El horario 1 es obligatorio.',
            'idHorario1.exists' => 'El horario 1 seleccionado no existe.',
            'idHorario2.exists' => 'El horario 2 seleccionado no existe.',
            'idHorario3.exists' => 'El horario 3 seleccionado no existe.',
            'idPeriodo.required' => 'El regimen academico es obligatorio.',
            'idPeriodo.exists' => 'El regimen academico seleccionado no existe.'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $h1Id = $this->input('idHorario1');
            $h2Id = $this->input('idHorario2');
            $h3Id = $this->input('idHorario3');
            $cursoId = $this->input('idCurso');
            $docenteId = $this->input('idDocente');
            $periodId = $this->input('idPeriodo');

            // 0. Validar estados activos
            $materiaId = $this->input('idMateria');
            if ($materiaId) {
                $materia = \Illuminate\Support\Facades\DB::table('materias')->where('idMateria', $materiaId)->first();
                if ($materia && !$materia->estado) {
                    $validator->errors()->add('idMateria', 'La materia seleccionada está inactiva y no se puede usar para una nueva oferta.');
                }
            }

            if ($cursoId) {
                $cursoFisico = \Illuminate\Support\Facades\DB::table('cursos')->where('idCurso', $cursoId)->first();
                if ($cursoFisico && !$cursoFisico->estado) {
                    $validator->errors()->add('idCurso', 'El aula seleccionada está inactiva y no se puede usar para una nueva oferta.');
                }
            }

            if ($docenteId) {
                $docente = \Illuminate\Support\Facades\DB::table('usuarios')->where('idUsuario', $docenteId)->first();
                if ($docente && !$docente->estado) {
                    $validator->errors()->add('idDocente', 'El docente seleccionado está inactivo y no se puede usar para una nueva oferta.');
                }
            }

            if ($periodId) {
                $periodo = \Illuminate\Support\Facades\DB::table('periodos')->where('idPeriodo', $periodId)->first();
                if ($periodo && !$periodo->estado) {
                    $validator->errors()->add('idPeriodo', 'El periodo académico seleccionado está inactivo y no se puede usar para una nueva oferta.');
                }
            }

            // Validar que el primer horario esté presente
            if (!$h1Id) {
                return;
            }

            // Validar que no se escoja el tercer horario sin el segundo
            if ($h3Id && !$h2Id) {
                $validator->errors()->add('idHorario3', 'No se puede seleccionar el tercer horario si no se ha seleccionado el segundo.');
                return;
            }

            // Validar que los horarios seleccionados no sean duplicados
            if ($h2Id && $h1Id == $h2Id) {
                $validator->errors()->add('idHorario2', 'Los horarios seleccionados deben ser diferentes.');
                return;
            }
            if ($h3Id && ($h1Id == $h3Id || $h2Id == $h3Id)) {
                $validator->errors()->add('idHorario3', 'Los horarios seleccionados deben ser diferentes.');
                return;
            }

            // Validar continuidad entre el 1º y 2º horario (si se seleccionó el 2º)
            if ($h2Id) {
                $h1 = \Illuminate\Support\Facades\DB::table('horarios')->where('idHorario', $h1Id)->first();
                $h2 = \Illuminate\Support\Facades\DB::table('horarios')->where('idHorario', $h2Id)->first();

                if ($h1 && $h2) {
                    if ($h1->diaSemana != $h2->diaSemana) {
                        $validator->errors()->add('idHorario2', 'El segundo horario debe ser del mismo día que el primero.');
                    } else {
                        $t1Start = substr($h1->horaInicio, 0, 5);
                        $t1End = substr($h1->horaFin, 0, 5);
                        $t2Start = substr($h2->horaInicio, 0, 5);
                        $t2End = substr($h2->horaFin, 0, 5);

                        if ($t1End !== $t2Start && $t2End !== $t1Start) {
                            $validator->errors()->add('idHorario2', 'El segundo horario debe ser continuo al primero.');
                        }
                    }
                }
            }

            // Obtener todos los IDs de horarios seleccionados válidos
            $hIds = array_filter([$h1Id, $h2Id, $h3Id]);

            // Classroom/Aula overlap check
            if ($cursoId && $periodId && !empty($hIds)) {
                $classroomOverlap = \Illuminate\Support\Facades\DB::table('cursos_materias')
                    ->join('horariocurso', 'horariocurso.idCursoMateria', '=', 'cursos_materias.idCursoMateria')
                    ->where('cursos_materias.idPeriodo', $periodId)
                    ->where('cursos_materias.estado', 1)
                    ->where('cursos_materias.idCurso', $cursoId)
                    ->whereIn('horariocurso.idHorario', $hIds)
                    ->exists();

                if ($classroomOverlap) {
                    $validator->errors()->add('idCurso', 'El aula seleccionada ya tiene materias asignadas en esos horarios para el periodo actual.');
                }
            }

            // Docente overlap check
            if ($docenteId && $periodId && !empty($hIds)) {
                $docenteOverlap = \Illuminate\Support\Facades\DB::table('cursos_materias')
                    ->join('horariocurso', 'horariocurso.idCursoMateria', '=', 'cursos_materias.idCursoMateria')
                    ->where('cursos_materias.idPeriodo', $periodId)
                    ->where('cursos_materias.estado', 1)
                    ->where('cursos_materias.idDocente', $docenteId)
                    ->whereIn('horariocurso.idHorario', $hIds)
                    ->exists();

                if ($docenteOverlap) {
                    $validator->errors()->add('idDocente', 'El docente seleccionado ya tiene clases asignadas en esos horarios para el periodo actual.');
                }
            }
        });
    }
}
