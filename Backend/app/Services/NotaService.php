<?php

namespace App\Services;

use App\Repositories\Contracts\NotaRepositoryInterface;

class NotaService
{
    public function __construct(
        private readonly NotaRepositoryInterface $notas,
    ) {
    }

    public function store(array $validated): array
    {
        $nota = $this->notas->create([
            'docente_id' => $validated['docente_id'],
            'estudiante_materia_id' => $validated['estudiante_materia_id'],
            'nota' => $validated['nota'],
        ]);

        return $this->payload($nota);
    }

    public function update(int $docenteId, int $notaId, array $validated): array
    {
        $nota = $this->notas->updateNota($docenteId, $notaId, (float) $validated['nota']);

        return $this->payload($nota);
    }

    private function payload(array $nota): array
    {
        return [
            'idNota'       => (int)   $nota['idNota'],
            'idInscripcion'=> (int)   $nota['idInscripcion'],
            'nota'         => (float) $nota['nota'],
            'fechaRegistro'=> $nota['fechaRegistro'] ?? null,
            'estado'       => (bool)  ($nota['estado'] ?? true),
        ];
    }
}
