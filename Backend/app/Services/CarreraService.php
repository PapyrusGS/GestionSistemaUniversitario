<?php

namespace App\Services;

use App\Models\Carrera;
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
        return $this->carreras->allOrdered()->map(fn (Carrera $carrera) => $this->payload($carrera));
    }

    public function store(array $validated): array
    {
        $carrera = $this->carreras->create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'estado' => true,
            'fechaRegistro' => now(),
        ]);

        return $this->payload($carrera);
    }

    public function update(int $id, array $validated): array
    {
        $carrera = $this->carreras->findOrFail($id);
        $carrera->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? $carrera->descripcion,
        ]);

        return $this->payload($carrera->fresh());
    }

    public function destroy(int $id): array
    {
        $carrera = $this->carreras->findOrFail($id);

        if (! $carrera->estado) {
            return $this->payload($carrera);
        }

        $carrera->update(['estado' => false]);

        return $this->payload($carrera->fresh());
    }

    private function payload(Carrera $carrera): array
    {
        return [
            'idCarrera' => $carrera->idCarrera,
            'nombre' => $carrera->nombre,
            'descripcion' => $carrera->descripcion,
            'estado' => (bool) $carrera->estado,
            'fechaRegistro' => $carrera->fechaRegistro?->format('Y-m-d H:i'),
        ];
    }
}
