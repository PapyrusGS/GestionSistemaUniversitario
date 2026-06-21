<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_find');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_list()
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        ANY_VALUE(hc.idHorario) AS idHorario,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.maxInscritos,
        cm.fechaRegistro,
        CAST(cm.estado AS UNSIGNED) AS estado,
        ANY_VALUE(m.nombre) AS materia,
        ANY_VALUE(CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2)) AS docente,
        ANY_VALUE(p.nombre) AS periodo,
        GROUP_CONCAT(DISTINCT CONCAT('Dia ', h.diaSemana, ' ', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i')) SEPARATOR ', ') AS horarioDetalle
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN usuarios u ON u.idUsuario = cm.idDocente
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    LEFT JOIN horariocurso hc ON hc.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
    LEFT JOIN horarios h ON h.idHorario = hc.idHorario
    GROUP BY
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.maxInscritos,
        cm.fechaRegistro,
        cm.estado
    ORDER BY cm.fechaRegistro DESC, cm.idCursoMateria DESC;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_find(IN p_idCursoMateria BIGINT UNSIGNED)
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        ANY_VALUE(hc.idHorario) AS idHorario,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.maxInscritos,
        cm.fechaRegistro,
        CAST(cm.estado AS UNSIGNED) AS estado,
        ANY_VALUE(m.nombre) AS materia,
        ANY_VALUE(CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2)) AS docente,
        ANY_VALUE(p.nombre) AS periodo,
        GROUP_CONCAT(DISTINCT CONCAT('Dia ', h.diaSemana, ' ', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i')) SEPARATOR ', ') AS horarioDetalle
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN usuarios u ON u.idUsuario = cm.idDocente
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    LEFT JOIN horariocurso hc ON hc.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
    LEFT JOIN horarios h ON h.idHorario = hc.idHorario
    WHERE cm.idCursoMateria = p_idCursoMateria
    GROUP BY
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.maxInscritos,
        cm.fechaRegistro,
        cm.estado
    LIMIT 1;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_list');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_list()
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.maxInscritos,
        cm.fechaRegistro,
        CAST(cm.estado AS UNSIGNED) AS estado,
        m.nombre AS materia,
        CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2) AS docente,
        p.nombre AS periodo,
        GROUP_CONCAT(DISTINCT CONCAT('Dia ', h.diaSemana, ' ', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i')) SEPARATOR ', ') AS horarioDetalle
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN usuarios u ON u.idUsuario = cm.idDocente
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    LEFT JOIN horariocurso hc ON hc.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
    LEFT JOIN horarios h ON h.idHorario = hc.idHorario
    GROUP BY
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.maxInscritos,
        cm.fechaRegistro,
        cm.estado,
        m.nombre,
        p.nombre
    ORDER BY cm.fechaRegistro DESC, cm.idCursoMateria DESC;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_find(IN p_idCursoMateria BIGINT UNSIGNED)
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.maxInscritos,
        cm.fechaRegistro,
        CAST(cm.estado AS UNSIGNED) AS estado,
        m.nombre AS materia,
        CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2) AS docente,
        p.nombre AS periodo,
        GROUP_CONCAT(DISTINCT CONCAT('Dia ', h.diaSemana, ' ', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i')) SEPARATOR ', ') AS horarioDetalle
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN usuarios u ON u.idUsuario = cm.idDocente
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    LEFT JOIN horariocurso hc ON hc.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
    LEFT JOIN horarios h ON h.idHorario = hc.idHorario
    WHERE cm.idCursoMateria = p_idCursoMateria
    GROUP BY
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.maxInscritos,
        cm.fechaRegistro,
        cm.estado,
        m.nombre,
        p.nombre
    LIMIT 1;
END
SQL);
    }
};
