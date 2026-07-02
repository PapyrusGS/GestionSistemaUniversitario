<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_cursos_listar');

        DB::unprepared("
            CREATE PROCEDURE sp_docente_cursos_listar(IN p_idDocente BIGINT UNSIGNED)
            BEGIN
                SELECT 
                    cm.idCursoMateria,
                    cm.idCurso,
                    cm.idMateria,
                    DATE_FORMAT(cm.fechaInicio, '%Y-%m-%d') AS fechaInicio,
                    DATE_FORMAT(cm.fechaFin, '%Y-%m-%d') AS fechaFin,
                    c.capacidad AS max_inscritos, 
                    c.capacidad AS cupo,          
                    c.capacidad AS cupo_maximo,
                    m.nombre AS materia_nombre,
                    m.semestre AS materia_semestre,
                    (
                        SELECT COUNT(*) 
                        FROM estudiantemateria em 
                        WHERE em.idCursoMateria = cm.idCursoMateria AND em.estado = 1
                    ) AS alumnos_count,
                    IFNULL(
                        (SELECT GROUP_CONCAT(CONCAT(h.diaSemana, ' (', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i'), ')') SEPARATOR ' / ')
                         FROM horariocurso hc
                         INNER JOIN horarios h ON hc.idHorario = h.idHorario
                         WHERE hc.idCursoMateria = cm.idCursoMateria), 
                        'Sin horario'
                    ) AS turno_nombre
                FROM cursos_materias cm
                INNER JOIN materias m ON cm.idMateria = m.idMateria
                INNER JOIN cursos c ON cm.idCurso = c.idCurso
                WHERE cm.idDocente = p_idDocente 
                  AND cm.estado = 1
                  AND m.estado = 1
                  AND c.estado = 1;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_cursos_listar');

        DB::unprepared("
            CREATE PROCEDURE sp_docente_cursos_listar(IN p_idDocente BIGINT UNSIGNED)
            BEGIN
                SELECT 
                    cm.idCursoMateria,
                    cm.idCurso,
                    cm.idMateria,
                    DATE_FORMAT(cm.fechaInicio, '%Y-%m-%d') AS fechaInicio,
                    DATE_FORMAT(cm.fechaFin, '%Y-%m-%d') AS fechaFin,
                    c.capacidad AS max_inscritos, 
                    c.capacidad AS cupo,          
                    c.capacidad AS cupo_maximo,
                    m.nombre AS materia_nombre,
                    (
                        SELECT COUNT(*) 
                        FROM estudiantemateria em 
                        WHERE em.idCursoMateria = cm.idCursoMateria AND em.estado = 1
                    ) AS alumnos_count,
                    IFNULL(
                        (SELECT GROUP_CONCAT(CONCAT(h.diaSemana, ' (', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i'), ')') SEPARATOR ' / ')
                         FROM horariocurso hc
                         INNER JOIN horarios h ON hc.idHorario = h.idHorario
                         WHERE hc.idCursoMateria = cm.idCursoMateria), 
                        'Sin horario'
                    ) AS turno_nombre
                FROM cursos_materias cm
                INNER JOIN materias m ON cm.idMateria = m.idMateria
                INNER JOIN cursos c ON cm.idCurso = c.idCurso
                WHERE cm.idDocente = p_idDocente 
                  AND cm.estado = 1
                  AND m.estado = 1
                  AND c.estado = 1;
            END;
        ");
    }
};
