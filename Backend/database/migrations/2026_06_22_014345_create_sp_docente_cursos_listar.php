<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Instala el Stored Procedure definitivo en la base de datos de todo el equipo.
     */
    public function up(): void
{
    // Eliminamos el procedimiento si ya existía para evitar colisiones de nombres
    DB::unprepared("DROP PROCEDURE IF EXISTS sp_docente_cursos_listar;");

    // Creamos el Stored Procedure adaptado a tu NUEVO diseño de tablas
    DB::unprepared("
        CREATE PROCEDURE sp_docente_cursos_listar(IN p_idDocente BIGINT UNSIGNED)
        BEGIN
            SELECT 
                cm.idCursoMateria,
                cm.idCurso,
                cm.idMateria,
                cm.fechaInicio,
                cm.fechaFin,
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
     * Elimina el procedimiento si se realiza un rollback manual de las migraciones.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_docente_cursos_listar;");
    }
};