<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class MateriaDocenteRepository
{
    /**
     * Ejecuta el procedimiento almacenado para listar materias dictadas por el docente
     *
     * @param int $idDocente
     * @return array
     */
    public function listarCursos(int $idDocente): array
    {
        return DB::select('CALL sp_docente_cursos_listar(?)', [$idDocente]);
    }

    /**
     * Valida si el curso materia pertenece al docente autenticado
     *
     * @param int $idCursoMateria
     * @param int $idDocente
     * @return bool
     */
    public function verificarPropiedadCurso(int $idCursoMateria, int $idDocente): bool
    {
        $resultado = DB::select('SELECT idDocente FROM cursos_materias WHERE idCursoMateria = ? LIMIT 1', [$idCursoMateria]);
        
        if (empty($resultado)) {
            return false;
        }

        return (int)$resultado[0]->idDocente === $idDocente;
    }

    /**
     * Ejecuta el procedimiento almacenado para listar los alumnos matriculados
     *
     * @param int $idCursoMateria
     * @return array
     */
    public function listarEstudiantesPorCurso(int $idCursoMateria): array
    {
        return DB::select('CALL sp_curso_estudiantes_listar(?)', [$idCursoMateria]);
    }
}