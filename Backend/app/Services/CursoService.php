<?php

namespace App\Services;

use App\Repositories\Contracts\CursoRepositoryInterface;
use App\Repositories\Contracts\DocenteRepositoryInterface;
use App\Repositories\Contracts\HorarioRepositoryInterface;
use App\Repositories\Contracts\MateriaRepositoryInterface;
use App\Repositories\Contracts\PeriodoRepositoryInterface;
use Illuminate\Support\Collection;

class CursoService
{
    public function __construct(
        private readonly CursoRepositoryInterface $cursos,
        private readonly MateriaRepositoryInterface $materias,
        private readonly DocenteRepositoryInterface $docentes,
        private readonly HorarioRepositoryInterface $horarios,
        private readonly PeriodoRepositoryInterface $periodos,
    ) {
    }

    public function index(): Collection
    {
        return $this->cursos->allOrdered()->map(fn (array $curso) => $this->payload($curso));
    }

    public function formData(): array
    {
        return [
            'cursosFisicos' => $this->cursos->activePhysicalForSelect()->values(),
            'materias' => $this->materias->active()->values(),
            'docentes' => $this->docentes->activeForSelect()->values(),
            'horarios' => $this->horarios->activeForSelect()->values(),
            'periodos' => $this->periodos->activeForSelect()->values(),
        ];
    }

    public function store(array $validated): array
    {
        $curso = $this->cursos->create($validated);

        return $this->payload($curso);
    }

    public function destroy($id): array
    {
        return $this->payload($this->cursos->disable($id));
    }

    private function payload(array $curso): array
    {
        return [
            'idCursoMateria' => $curso['idCursoMateria'],
            'idCurso' => $curso['idCurso'],
            'idMateria' => $curso['idMateria'],
            'idDocente' => (int) $curso['idDocente'],
            'idPeriodo' => (int) $curso['idPeriodo'],
            'idHorario' => $curso['idHorario'] ?? null,
            'fechaInicio' => $curso['fechaInicio'] ?? null,
            'fechaFin' => $curso['fechaFin'] ?? null,
            'estado' => (bool) ($curso['estado'] ?? true),
            'materia' => $curso['materia'] ?? null,
            'docente' => $curso['docente'] ?? null,
            'periodo' => $curso['periodo'] ?? null,
            'horario' => $curso['horario'] ?? null,
            'horarioDetalle' => $curso['horarioDetalle'] ?? null,
        ];
    }
}
