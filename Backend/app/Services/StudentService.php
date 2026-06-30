<?php

namespace App\Services;

use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentService
{
    private const NOTA_APROBACION = 51;

    private const DAY_NAMES = [
        1 => 'Lun', 2 => 'Mar', 3 => 'Mie', 4 => 'Jue',
        5 => 'Vie', 6 => 'Sab', 7 => 'Dom',
    ];

    public function __construct(
        private readonly StudentRepositoryInterface $students,
    ) {
    }

    public function getStudentOrFail(Request $request): object
    {
        $user = $request->user()->loadMissing('rol');

        if ($user->rol?->nombre !== 'Estudiante') {
            abort(403, 'Solo los estudiantes pueden acceder a este modulo.');
        }

        $student = $this->students->profile((int) $user->idUsuario);

        if (! $student) {
            abort(404, 'No existe un registro de estudiante para el usuario autenticado.');
        }

        return $student;
    }

    public function dashboard(object $student): array
    {
        $career = $this->students->career((int) $student->idEstudiante);
        $enrolled = $this->students->enrolledSubjects((int) $student->idEstudiante);
        $grades = $this->students->grades((int) $student->idEstudiante);

        $gradesCollection = $this->mapGrades($grades);

        return [
            'perfil' => [
                'idEstudiante' => $student->idEstudiante,
                'idUsuario' => $student->idUsuario,
                'nombreCompleto' => $student->nombreCompleto,
                'correo' => $student->correo,
                'ci' => $student->ci,
                'carrera' => $career?->carrera,
            ],
            'resumen' => [
                'materiasInscritas' => $enrolled->count(),
                'notasRegistradas' => $grades->count(),
                'aprobadas' => $gradesCollection->where('estadoAcademico', 'Aprobada')->count(),
                'reprobadas' => $gradesCollection->where('estadoAcademico', 'Reprobada')->count(),
            ],
        ];
    }

    public function materiasDisponibles(object $student): Collection
    {
        $career = $this->students->career((int) $student->idEstudiante);

        if (! $career) {
            return collect();
        }

        $approvedMateriaIds = $this->students->approvedMateriaIds((int) $student->idEstudiante);
        $enrolledCursoMateriaIds = $this->students->enrolledCursoMateriaIds((int) $student->idEstudiante);

        return $this->students->availableSubjects((int) $student->idEstudiante, (int) $career->idCarrera)
            ->map(fn ($subject) => $this->mapSubjectPayload($subject, $approvedMateriaIds, $enrolledCursoMateriaIds, (int) $student->idEstudiante));
    }

    public function inscribir(object $student, int $cursoMateriaId): int
    {
        $career = $this->students->career((int) $student->idEstudiante);
        if (! $career) {
            throw ValidationException::withMessages([
                'idCursoMateria' => ['El estudiante no tiene una carrera activa asignada.'],
            ]);
        }

        $availableSubjects = $this->students->availableSubjects((int) $student->idEstudiante, (int) $career->idCarrera);
        $subject = $availableSubjects->firstWhere('idCursoMateria', $cursoMateriaId);

        if (! $subject) {
            throw ValidationException::withMessages([
                'idCursoMateria' => ['La materia seleccionada no esta disponible.'],
            ]);
        }

        if ((int) $subject->inscritos >= (int) $subject->maxInscritos) {
            throw ValidationException::withMessages([
                'idCursoMateria' => ['La materia seleccionada no tiene cupos disponibles.'],
            ]);
        }

        $enrolledIds = $this->students->enrolledCursoMateriaIds((int) $student->idEstudiante);
        if ($enrolledIds->contains($cursoMateriaId)) {
            throw ValidationException::withMessages([
                'idCursoMateria' => ['Ya estas inscrito en esta materia.'],
            ]);
        }

        $approvedIds = $this->students->approvedMateriaIds((int) $student->idEstudiante);
        if ($subject->idMateriaPrevia && ! $approvedIds->contains((string) $subject->idMateriaPrevia)) {
            throw ValidationException::withMessages([
                'idCursoMateria' => ["Debes aprobar previamente: {$subject->prerrequisito}."],
            ]);
        }

        $conflict = $this->students->scheduleConflict((int) $student->idEstudiante, (int) $subject->idCursoMateria);
        if ($conflict) {
            throw ValidationException::withMessages([
                'idCursoMateria' => ["Existe cruce de horario con {$conflict->materia}."],
            ]);
        }

        return $this->students->enroll(
            (int) $student->idEstudiante,
            $cursoMateriaId,
            (int) $student->idUsuario,
            request()->ip()
        );
    }

    public function inscripciones(object $student): Collection
    {
        return $this->students->enrolledSubjects((int) $student->idEstudiante)
            ->map(fn ($item) => [
                'idInscripcion' => $item->idInscripcion,
                'materia' => $item->materia,
                'semestre' => (int) $item->semestre,
                'docente' => $item->docente,
                'periodo' => $item->periodo,
                'horario' => $this->scheduleText((int) $item->idCursoMateria),
                'fecha' => $item->fecha,
                'estado' => ((int) $item->estado) === 1 ? 'Activa' : 'Inactiva',
            ]);
    }

    public function notas(object $student): Collection
    {
        return $this->mapGrades($this->students->grades((int) $student->idEstudiante));
    }

    public function historial(object $student): Collection
    {
        $career = $this->students->career((int) $student->idEstudiante);
        if (! $career) {
            return collect();
        }

        return $this->students->curriculum((int) $student->idEstudiante, (int) $career->idCarrera)
            ->map(fn ($subject) => $this->mapCurriculumItem($subject))
            ->groupBy('semestre')
            ->map(fn ($items, $semester) => ['semestre' => (int) $semester, 'materias' => $items->values()])
            ->values();
    }

    public function malla(object $student): Collection
    {
        $career = $this->students->career((int) $student->idEstudiante);
        if (! $career) {
            return collect();
        }

        return $this->students->curriculum((int) $student->idEstudiante, (int) $career->idCarrera)
            ->map(fn ($subject) => $this->mapCurriculumItem($subject))
            ->groupBy('semestre')
            ->map(fn ($items, $semester) => ['semestre' => (int) $semester, 'materias' => $items->values()])
            ->values();
    }

    public function getStudentCareer(object $student): ?object
    {
        return $this->students->career((int) $student->idEstudiante);
    }

    public function reporte(Request $request, object $student, string $tipo): Collection
    {
        $data = match ($tipo) {
            'inscripciones' => $this->inscripciones($student),
            'notas' => $this->notas($student),
            'historial' => $this->reporteHistorialPlano($student),
            default => collect(),
        };

        DB::table('reportes')->insert([
            'tipo' => $tipo,
            'filtros' => 'estudiante=' . $student->idEstudiante,
            'idUsuario' => $student->idUsuario,
            'fechaGeneracion' => now(),
            'fechaA' => now(),
            'UsuarioA' => (string) $student->idUsuario,
            'estadoA' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $data;
    }

    private function reporteHistorialPlano(object $student): Collection
    {
        $career = $this->students->career((int) $student->idEstudiante);
        if (! $career) {
            return collect();
        }

        return $this->students->curriculum((int) $student->idEstudiante, (int) $career->idCarrera)
            ->map(fn ($subject) => $this->mapCurriculumItem($subject));
    }

    private function mapSubjectPayload(object $subject, Collection $approvedMateriaIds, Collection $enrolledCursoMateriaIds, int $studentId): array
    {
        $hasPrereq = ! $subject->idMateriaPrevia || $approvedMateriaIds->contains((string) $subject->idMateriaPrevia);
        $hasSlot = (int) $subject->inscritos < (int) $subject->maxInscritos;
        $notEnrolled = ! $enrolledCursoMateriaIds->contains((int) $subject->idCursoMateria);

        return [
            'idCursoMateria' => $subject->idCursoMateria,
            'idCurso' => $subject->idCurso,
            'idMateria' => $subject->idMateria,
            'materia' => $subject->materia,
            'semestre' => (int) $subject->semestre,
            'docente' => $subject->docente,
            'horario' => $this->scheduleText((int) $subject->idCursoMateria),
            'fechaInicio' => $subject->fechaInicio,
            'fechaFin' => $subject->fechaFin,
            'cupoTotal' => (int) $subject->maxInscritos,
            'cuposDisponibles' => max(0, (int) $subject->maxInscritos - (int) $subject->inscritos),
            'prerrequisito' => $subject->prerrequisito,
            'regimen' => $subject->periodo,
            'puedeInscribirse' => $notEnrolled && $hasPrereq && $hasSlot,
        ];
    }

    private function mapGrades(Collection $grades): Collection
    {
        return $grades->map(fn ($grade) => [
            'idMateria' => $grade->idMateria,
            'materia' => $grade->materia,
            'semestre' => (int) $grade->semestre,
            'periodo' => $grade->periodo,
            'nota' => (float) $grade->nota,
            'estadoAcademico' => (float) $grade->nota >= self::NOTA_APROBACION ? 'Aprobada' : 'Reprobada',
        ]);
    }

    private function mapCurriculumItem(object $subject): array
    {
        $score = $subject->nota !== null ? (float) $subject->nota : null;

        $status = match (true) {
            $score !== null && $score >= self::NOTA_APROBACION => 'Aprobada',
            $score !== null => 'Reprobada',
            (int) $subject->inscrita === 1 => 'Inscrita',
            default => 'Pendiente',
        };

        return [
            'idMateria' => $subject->idMateria,
            'semestre' => (int) $subject->semestre,
            'materia' => $subject->materia,
            'nota' => $score,
            'estadoAcademico' => $status,
        ];
    }

    private function scheduleText(int $cursoMateriaId): string
    {
        return $this->students->scheduleSlots($cursoMateriaId)
            ->map(fn ($slot) => (self::DAY_NAMES[(int) $slot->diaSemana] ?? "Dia {$slot->diaSemana}") . ' ' . substr($slot->horaInicio, 0, 5) . '-' . substr($slot->horaFin, 0, 5))
            ->implode(', ');
    }
}
