<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_horarios_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_periodos_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docentes_active');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_horarios_active()
BEGIN
    SELECT
        h.idHorario,
        h.diaSemana,
        h.horaInicio,
        h.horaFin,
        CONCAT('Dia ', h.diaSemana, ' - ', TIME_FORMAT(h.horaInicio, '%H:%i'), ' a ', TIME_FORMAT(h.horaFin, '%H:%i')) AS descripcion
    FROM horarios h
    ORDER BY h.diaSemana, h.horaInicio;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_periodos_active()
BEGIN
    SELECT
        p.idPeriodo,
        p.nombre,
        p.fechaInicioSemestre,
        p.fechaFinSemestre
    FROM periodos p
    WHERE p.estado = 1
    ORDER BY p.fechaInicioSemestre DESC, p.idPeriodo DESC;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docentes_active()
BEGIN
    SELECT
        u.idUsuario,
        CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2) AS nombreCompleto,
        u.correo,
        u.ci
    FROM usuarios u
    INNER JOIN roles r ON r.idRol = u.idRol
    WHERE r.nombre COLLATE utf8mb4_unicode_ci = 'Docente'
      AND u.estado = 1
    ORDER BY u.apellido1, u.apellido2, u.nombre1, u.nombre2;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_list()
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        MIN(hc.idHorario) AS idHorario,
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
        MIN(hc.idHorario) AS idHorario,
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

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_store(
    IN p_idMateria VARCHAR(100),
    IN p_idDocente BIGINT UNSIGNED,
    IN p_idHorario BIGINT UNSIGNED,
    IN p_idPeriodo BIGINT UNSIGNED,
    IN p_maxInscritos INT
)
BEGIN
    DECLARE v_idCurso VARCHAR(100);
    DECLARE v_fechaInicio DATE;
    DECLARE v_fechaFin DATE;
    DECLARE v_idCursoMateria BIGINT UNSIGNED;

    SELECT CONCAT('CUR-', LPAD(COALESCE(MAX(CAST(SUBSTRING(idCurso, 5) AS UNSIGNED)), 0) + 1, 3, '0'))
    INTO v_idCurso
    FROM cursos;

    SELECT fechaInicioSemestre, fechaFinSemestre
    INTO v_fechaInicio, v_fechaFin
    FROM periodos
    WHERE idPeriodo = p_idPeriodo
    LIMIT 1;

    INSERT INTO cursos (
        idCurso,
        capacidad,
        estado,
        fechaA,
        UsuarioA,
        estadoA,
        created_at,
        updated_at
    ) VALUES (
        v_idCurso,
        p_maxInscritos,
        1,
        NOW(),
        NULL,
        1,
        NOW(),
        NOW()
    );

    INSERT INTO cursos_materias (
        idCurso,
        idMateria,
        idDocente,
        idPeriodo,
        fechaInicio,
        fechaFin,
        maxInscritos,
        fechaRegistro,
        estado,
        fechaA,
        UsuarioA,
        estadoA,
        created_at,
        updated_at
    ) VALUES (
        v_idCurso,
        p_idMateria,
        p_idDocente,
        p_idPeriodo,
        v_fechaInicio,
        v_fechaFin,
        p_maxInscritos,
        NOW(),
        1,
        NOW(),
        NULL,
        1,
        NOW(),
        NOW()
    );

    SET v_idCursoMateria = LAST_INSERT_ID();

    INSERT INTO horariocurso (
        idHorario,
        idCurso,
        fechaA,
        UsuarioA,
        estadoA,
        created_at,
        updated_at
    ) VALUES (
        p_idHorario,
        v_idCurso,
        NOW(),
        NULL,
        1,
        NOW(),
        NOW()
    );

    CALL sp_cursos_find(v_idCursoMateria);
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docentes_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_periodos_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_horarios_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_list');
    }
};
