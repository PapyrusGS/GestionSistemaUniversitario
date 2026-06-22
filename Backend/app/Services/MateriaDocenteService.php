<?php

namespace App\Services;

use App\Repositories\MateriaDocenteRepository;
use Exception;

class MateriaDocenteService
{
    protected MateriaDocenteRepository $repository;

    public function __construct(MateriaDocenteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obtenerCursosPorDocente(int $idDocente): array
    {
        return $this->repository->listarCursos($idDocente);
    }

    public function obtenerEstudiantesInscritos(int $idCursoMateria, int $idDocente): array
    {
        if (!$this->repository->verificarPropiedadCurso($idCursoMateria, $idDocente)) {
            throw new Exception("No está autorizado para auditar este curso.");
        }
        return $this->repository->listarEstudiantesPorCurso($idCursoMateria);
    }
}