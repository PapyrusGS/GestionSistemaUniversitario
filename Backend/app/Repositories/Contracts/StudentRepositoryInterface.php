<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface StudentRepositoryInterface
{
    public function profile(int $userId): ?object;

    public function career(int $studentId): ?object;

    public function availableSubjects(int $carreraId): Collection;

    public function enrolledSubjects(int $studentId): Collection;

    public function grades(int $studentId): Collection;

    public function curriculum(int $studentId, int $carreraId): Collection;

    public function scheduleSlots(int $cursoMateriaId): Collection;

    public function approvedMateriaIds(int $studentId): Collection;

    public function enrolledCursoMateriaIds(int $studentId): Collection;

    public function scheduleConflict(int $studentId, int $newCursoMateriaId): ?object;

    public function enroll(int $studentId, int $cursoMateriaId, int $userId, string $ip): int;
}
