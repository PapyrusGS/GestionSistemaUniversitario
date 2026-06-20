<?php

namespace App\Services;

use App\Repositories\Contracts\CarreraRepositoryInterface;
use Illuminate\Support\Collection;

class CarreraService
{
    public function __construct(
        private readonly CarreraRepositoryInterface $carreras,
    ) {
    }

    public function index(): Collection
    {
        return $this->carreras->allOrdered()->map(fn (array $carrera) => $this->payload($carrera));
    }

    public function store(array $validated): array
    {
        $carrera = $this->carreras->create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        return $this->payload($carrera);
    }

    public function update(int $id, array $validated): array
    {
        return $this->payload($this->carreras->update($id, $validated));
    }

    public function destroy(int $id): array
    {
        return $this->payload($this->carreras->destroy($id));
    }

    private function payload(array $carrera): array
    {
        return [
            'idCarrera' => (int) $carrera['idCarrera'],
            'nombre' => $carrera['nombre'],
            'descripcion' => $carrera['descripcion'] ?? null,
            'estado' => (bool) ($carrera['estado'] ?? true),
            'fechaRegistro' => $carrera['fechaRegistro'] ?? null,
        ];
    }
}
