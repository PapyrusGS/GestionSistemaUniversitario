<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrects sp_cursos_list, sp_cursos_find, sp_cursos_find and sp_cursos_disable
 * to JOIN horariocurso using hc.idCurso (the real column) instead of
 * hc.idCursoMateria (which does not exist in the production table).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_disable');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_list()
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        (
            SELECT MIN(hc2.idHorario)
            FROM horariocurso hc2
            WHERE hc2.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
        ) AS idHorario,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.fechaRegistro,
        cm.maxInscritos,
        CAST(cm.estado AS UNSIGNED) AS estado,
        (
            SELECT m.nombre
            FROM materias m
            WHERE m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
            LIMIT 1
        ) AS materia,
        (
            SELECT CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2)
            FROM usuarios u
            WHERE u.idUsuario = cm.idDocente
            LIMIT 1
        ) AS docente,
        (
            SELECT p.nombre
            FROM periodos p
            WHERE p.idPeriodo = cm.idPeriodo
            LIMIT 1
        ) AS periodo,
        (
            SELECT GROUP_CONCAT(
                DISTINCT CONCAT('Dia ', h.diaSemana, ' ', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i'))
                SEPARATOR ', '
            )
            FROM horariocurso hc
            INNER JOIN horarios h ON h.idHorario = hc.idHorario
            WHERE hc.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
        ) AS horarioDetalle
    FROM cursos_materias cm
    ORDER BY cm.fechaRegistro DESC, cm.idCursoMateria DESC;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_find(IN p_idCursoMateria BIGINT UNSIGNED)
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        (
            SELECT MIN(hc2.idHorario)
            FROM horariocurso hc2
            WHERE hc2.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
        ) AS idHorario,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.fechaRegistro,
        cm.maxInscritos,
        CAST(cm.estado AS UNSIGNED) AS estado,
        (
            SELECT m.nombre
            FROM materias m
            WHERE m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
            LIMIT 1
        ) AS materia,
        (
            SELECT CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2)
            FROM usuarios u
            WHERE u.idUsuario = cm.idDocente
            LIMIT 1
        ) AS docente,
        (
            SELECT p.nombre
            FROM periodos p
            WHERE p.idPeriodo = cm.idPeriodo
            LIMIT 1
        ) AS periodo,
        (
            SELECT GROUP_CONCAT(
                DISTINCT CONCAT('Dia ', h.diaSemana, ' ', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i'))
                SEPARATOR ', '
            )
            FROM horariocurso hc
            INNER JOIN horarios h ON h.idHorario = hc.idHorario
            WHERE hc.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
        ) AS horarioDetalle
    FROM cursos_materias cm
    WHERE cm.idCursoMateria = p_idCursoMateria
    LIMIT 1;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_disable(IN p_idCursoMateria BIGINT UNSIGNED)
BEGIN
    UPDATE cursos_materias
    SET estado = 0,
        updated_at = NOW()
    WHERE idCursoMateria = p_idCursoMateria;

    CALL sp_cursos_find(p_idCursoMateria);
END
SQL);
    }

    public function down(): void
    {
        // Restored in previous migration's down() — no additional rollback needed
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_disable');
    }
};
