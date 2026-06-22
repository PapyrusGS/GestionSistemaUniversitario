<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSpDocenteModulo extends Migration
{
    public function up(): void
    {
        // Limpieza preventiva
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_cursos_listar');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_curso_estudiantes_listar');

        // HU-DOC-02: Listar cursos del docente
        DB::unprepared("
            CREATE PROCEDURE sp_docente_cursos_listar(IN p_idDocente BIGINT UNSIGNED)
            BEGIN
                SELECT 
                    cm.idCursoMateria,
                    cm.idCurso,
                    cm.idMateria,
                    cm.fechaInicio,
                    cm.fechaFin,
                    cm.maxInscritos,
                    m.nombre AS materia_nombre,
                    t.nombre AS turno_nombre
                FROM cursos_materias cm
                INNER JOIN materias m ON cm.idMateria = m.idMateria
                INNER JOIN turnos t ON cm.idTurno = t.idTurno
                WHERE cm.idDocente = p_idDocente AND cm.estado = 1;
            END
        ");

        // HU-DOC-03: Listar alumnos inscritos
        DB::unprepared("
            CREATE PROCEDURE sp_curso_estudiantes_listar(IN p_idCursoMateria BIGINT UNSIGNED)
            BEGIN
                SELECT 
                    u.idUsuario AS id_estudiante,
                    u.ci,
                    u.nombre1,
                    u.nombre2,
                    u.apellido1,
                    u.apellido2,
                    u.correo,
                    i.fecha AS fecha_inscripcion
                FROM inscripciones i
                INNER JOIN usuarios u ON i.idEstudiante = u.idUsuario
                WHERE i.idCursoMateria = p_idCursoMateria AND i.estado = 1
                ORDER BY u.apellido1 ASC, u.nombre1 ASC;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_cursos_listar');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_curso_estudiantes_listar');
    }
}