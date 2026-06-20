<?php

namespace App\Services;

use App\Repositories\Contracts\CarreraRepositoryInterface;
use App\Repositories\Contracts\MateriaRepositoryInterface;
use Illuminate\Support\Collection;

class MateriaService
{
    public function __construct(
        private readonly MateriaRepositoryInterface $materias,
        private readonly CarreraRepositoryInterface $carreras,
    ) {
    }

    public function index(): Collection
    {
        return $this->materias->allOrdered()->map(fn (array $materia) => $this->payload($materia));
    }

    public function formData(): array
    {
        return [
            'carreras' => $this->carreras->activeForSelect()->values(),
            'materias' => $this->materias->active()->values(),
        ];
    }

    public function store(array $validated): array
    {
        $materia = $this->materias->create([
            'idMateria' => $validated['idMateria'],
            'idCarrera' => $validated['idCarrera'],
            'idMateriaPrevia' => $validated['idMateriaPrevia'] ?? null,
            'nombre' => $validated['nombre'],
            'semestre' => $validated['semestre'],
            'fechaRegistro' => now(),
            'estado' => true,
        ]);

        return $this->payload($materia);
    }

    public function update(string $id, array $validated): array
    {
        return $this->payload($this->materias->update($id, $validated));
    }

    public function destroy(string $id): array
    {
        return $this->payload($this->materias->disable($id));
    }

    private function payload(array $materia): array
    {
        return [
            'idMateria' => $materia['idMateria'],
            'idCarrera' => (int) $materia['idCarrera'],
            'idMateriaPrevia' => $materia['idMateriaPrevia'] ?? null,
            'nombre' => $materia['nombre'],
            'semestre' => (int) $materia['semestre'],
            'estado' => (bool) ($materia['estado'] ?? true),
            'fechaRegistro' => $materia['fechaRegistro'] ?? null,
            'carrera' => $materia['carrera'] ?? null,
            'prerrequisito' => $materia['prerrequisito'] ?? null,
        ];
    }
}
