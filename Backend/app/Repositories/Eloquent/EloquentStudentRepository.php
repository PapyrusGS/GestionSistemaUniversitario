<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentStudentRepository implements StudentRepositoryInterface
{
    public function profile(int $userId): ?object
    {
        $rows = DB::select('CALL sp_estudiante_profile(?)', [$userId]);

        return $rows[0] ?? null;
    }

    public function career(int $studentId): ?object
    {
        $rows = DB::select('CALL sp_estudiante_career(?)', [$studentId]);

        return $rows[0] ?? null;
    }

    public function availableSubjects(int $studentId, int $carreraId): Collection
    {
        return collect(DB::select('CALL sp_estudiante_materias_disponibles(?, ?)', [$studentId, $carreraId]));
    }

    public function enrolledSubjects(int $studentId): Collection
    {
        return collect(DB::select('CALL sp_estudiante_inscripciones(?)', [$studentId]));
    }

    public function grades(int $studentId): Collection
    {
        return collect(DB::select('CALL sp_estudiante_notas(?)', [$studentId]));
    }

    public function curriculum(int $studentId, int $carreraId): Collection
    {
        return collect(DB::select('CALL sp_estudiante_historial(?, ?)', [$studentId, $carreraId]));
    }

    public function scheduleSlots(int $cursoMateriaId): Collection
    {
        return collect(DB::select('CALL sp_estudiante_horario_texto(?)', [$cursoMateriaId]));
    }

    public function approvedMateriaIds(int $studentId): Collection
    {
        return collect(DB::select('CALL sp_estudiante_aprobadas(?)', [$studentId]))
            ->pluck('idMateria')
            ->map(fn ($id) => (string) $id);
    }

    public function enrolledCursoMateriaIds(int $studentId): Collection
    {
        return collect(DB::select('CALL sp_estudiante_inscritas_ids(?)', [$studentId]))
            ->pluck('idCursoMateria')
            ->map(fn ($id) => (int) $id);
    }

    public function scheduleConflict(int $studentId, int $newCursoMateriaId): ?object
    {
        $rows = DB::select('CALL sp_estudiante_horario_cruce(?, ?)', [$studentId, $newCursoMateriaId]);

        return $rows[0] ?? null;
    }

    public function enroll(int $studentId, int $cursoMateriaId, int $userId, string $ip): int
    {
        $rows = DB::select('CALL sp_estudiante_inscribir(?, ?, ?, ?)', [
            $studentId,
            $cursoMateriaId,
            $userId,
            $ip,
        ]);

        return (int) ($rows[0]->idInscripcion ?? 0);
    }
}
