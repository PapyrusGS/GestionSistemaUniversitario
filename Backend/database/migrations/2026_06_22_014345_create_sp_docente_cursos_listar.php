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
                    cm.fechaInicio,
                    cm.fechaFin,
                    cm.maxInscritos,
                    m.nombre AS materia_nombre,
                    IFNULL(
                        (SELECT GROUP_CONCAT(CONCAT(h.diaSemana, ' (', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i'), ')') SEPARATOR ' / ')
                         FROM horariocurso hc
                         INNER JOIN horarios h ON hc.idHorario = h.idHorario
                         WHERE hc.idCursoMateria = cm.idCursoMateria), 
                        'Sin horario'
                    ) AS turno_nombre
                FROM cursos_materias cm
                INNER JOIN materias m ON cm.idMateria = m.idMateria
                WHERE cm.idDocente = p_idDocente AND cm.estado = 1;
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