<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_docente_cursos_listar;");

        DB::unprepared("
            CREATE PROCEDURE sp_docente_cursos_listar(IN p_idDocente BIGINT UNSIGNED)
            BEGIN
                SELECT 
                    cm.idCursoMateria,
                    cm.idCurso,
                    cm.idMateria,
                    -- Formateamos las fechas para que solo devuelvan AAAA-MM-DD (Petición del docente)
                    DATE_FORMAT(cm.fechaInicio, '%Y-%m-%d') AS fechaInicio,
                    DATE_FORMAT(cm.fechaFin, '%Y-%m-%d') AS fechaFin,
                    -- Usamos c.capacidad (que sí existe) con todos los alias que espera tu Vue
                    c.capacidad AS max_inscritos, 
                    c.capacidad AS cupo,          
                    c.capacidad AS cupo_maximo,
                    m.nombre AS materia_nombre,
                    -- Conteo de alumnos inscritos en esta materia específica
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
                INNER JOIN cursos c ON cm.idCurso = c.idCurso -- Enlace con la tabla donde está el campo capacidad
                WHERE cm.idDocente = p_idDocente 
                  AND cm.estado = 1
                  AND m.estado = 1; -- <--- CORRECCIÓN CLAVE: Filtra para que no liste materias dadas de baja
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_docente_cursos_listar;");
    }
};