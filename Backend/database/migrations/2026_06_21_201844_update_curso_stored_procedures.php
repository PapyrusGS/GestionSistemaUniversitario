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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_find');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_active()
BEGIN
    SELECT
        c.idCurso,
        c.capacidad,
        c.estado
    FROM cursos c
    WHERE c.estado = 1
    ORDER BY c.idCurso;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_list()
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        (
            SELECT MIN(hc2.idHorario)
            FROM horariocurso hc2
            WHERE hc2.idCursoMateria = cm.idCursoMateria
        ) AS idHorario,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.fechaRegistro,
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
            WHERE hc.idCursoMateria = cm.idCursoMateria
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
            WHERE hc2.idCursoMateria = cm.idCursoMateria
        ) AS idHorario,
        cm.idMateria,
        cm.idDocente,
        cm.idPeriodo,
        cm.fechaInicio,
        cm.fechaFin,
        cm.fechaRegistro,
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
            WHERE hc.idCursoMateria = cm.idCursoMateria
        ) AS horarioDetalle
    FROM cursos_materias cm
    WHERE cm.idCursoMateria = p_idCursoMateria
    LIMIT 1;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_store(
    IN p_idCurso VARCHAR(100),
    IN p_idMateria VARCHAR(100),
    IN p_idDocente BIGINT UNSIGNED,
    IN p_idHorario1 BIGINT UNSIGNED,
    IN p_idHorario2 BIGINT UNSIGNED,
    IN p_idPeriodo BIGINT UNSIGNED
)
BEGIN
    DECLARE v_fechaInicio DATE;
    DECLARE v_fechaFin DATE;
    DECLARE v_idCursoMateria BIGINT UNSIGNED;

    -- Validación de Superposición de Aula/Curso Físico
    IF EXISTS (
        SELECT 1
        FROM cursos_materias cm
        INNER JOIN horariocurso hc ON hc.idCursoMateria = cm.idCursoMateria
        WHERE cm.idPeriodo = p_idPeriodo
          AND cm.estado = 1
          AND cm.idCurso COLLATE utf8mb4_unicode_ci = p_idCurso COLLATE utf8mb4_unicode_ci
          AND hc.idHorario IN (p_idHorario1, p_idHorario2)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El aula seleccionada ya tiene materias asignadas en esos horarios para el periodo actual.';
    END IF;

    -- Validación de Superposición de Docente
    IF EXISTS (
        SELECT 1
        FROM cursos_materias cm
        INNER JOIN horariocurso hc ON hc.idCursoMateria = cm.idCursoMateria
        WHERE cm.idPeriodo = p_idPeriodo
          AND cm.estado = 1
          AND cm.idDocente = p_idDocente
          AND hc.idHorario IN (p_idHorario1, p_idHorario2)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El docente seleccionado ya tiene clases asignadas en esos horarios para el periodo actual.';
    END IF;

    SELECT fechaInicioSemestre, fechaFinSemestre
    INTO v_fechaInicio, v_fechaFin
    FROM periodos
    WHERE idPeriodo = p_idPeriodo
    LIMIT 1;

    -- Asignamos la materia, docente y periodo al curso físico existente
    INSERT INTO cursos_materias (
        idCurso,
        idMateria,
        idDocente,
        idPeriodo,
        fechaInicio,
        fechaFin,
        fechaRegistro,
        estado,
        fechaA,
        UsuarioA,
        estadoA,
        created_at,
        updated_at
    ) VALUES (
        p_idCurso,
        p_idMateria,
        p_idDocente,
        p_idPeriodo,
        v_fechaInicio,
        v_fechaFin,
        NOW(),
        1,
        NOW(),
        NULL,
        1,
        NOW(),
        NOW()
    );

    SET v_idCursoMateria = LAST_INSERT_ID();

    -- Registramos el primer horario
    INSERT INTO horariocurso (
        idHorario,
        idCursoMateria,
        fechaA,
        UsuarioA,
        estadoA,
        created_at,
        updated_at
    ) VALUES (
        p_idHorario1,
        v_idCursoMateria,
        NOW(),
        NULL,
        1,
        NOW(),
        NOW()
    );

    -- Registramos el segundo horario
    INSERT INTO horariocurso (
        idHorario,
        idCursoMateria,
        fechaA,
        UsuarioA,
        estadoA,
        created_at,
        updated_at
    ) VALUES (
        p_idHorario2,
        v_idCursoMateria,
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_active');

        // Restaurar sp_cursos_list original (de 2026_06_20_000106)
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

        // Restaurar sp_cursos_find original (de 2026_06_20_000106)
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

        // Restaurar sp_cursos_store original (de 2026_06_20_000104)
        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_store(
    IN p_idMateria VARCHAR(100),
    IN p_idDocente BIGINT UNSIGNED,
    IN p_idHorario BIGINT UNSIGNED,
    IN p_idPeriodo BIGINT UNSIGNED
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
};
