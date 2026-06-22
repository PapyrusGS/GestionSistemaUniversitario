<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop old triggers / procedures
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_disable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_store');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_estudiantemateria_before_insert');

        // 2. Create sp_cursos_disable
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

        // 3. Create sp_cursos_store (with state checks)
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

    -- Validación: Materia activa
    IF EXISTS (
        SELECT 1 
        FROM materias 
        WHERE idMateria COLLATE utf8mb4_unicode_ci = p_idMateria COLLATE utf8mb4_unicode_ci 
          AND estado = 0
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La materia seleccionada está inactiva y no se puede usar para una nueva oferta.';
    END IF;

    -- Validación: Aula (curso físico) activa
    IF EXISTS (
        SELECT 1 
        FROM cursos 
        WHERE idCurso COLLATE utf8mb4_unicode_ci = p_idCurso COLLATE utf8mb4_unicode_ci 
          AND estado = 0
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El aula seleccionada está inactiva y no se puede usar para una nueva oferta.';
    END IF;

    -- Validación: Docente activo
    IF EXISTS (
        SELECT 1 
        FROM usuarios 
        WHERE idUsuario = p_idDocente 
          AND estado = 0
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El docente seleccionado está inactivo y no se puede usar para una nueva oferta.';
    END IF;

    -- Validación: Periodo activo
    IF EXISTS (
        SELECT 1 
        FROM periodos 
        WHERE idPeriodo = p_idPeriodo 
          AND estado = 0
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El periodo académico seleccionado está inactivo y no se puede usar para una nueva oferta.';
    END IF;

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

        // 4. Create trigger to block enrollment in inactive course offerings
        DB::unprepared(<<<SQL
CREATE TRIGGER tr_estudiantemateria_before_insert
BEFORE INSERT ON estudiantemateria
FOR EACH ROW
BEGIN
    DECLARE v_estado_curso TINYINT;
    SELECT estado INTO v_estado_curso FROM cursos_materias WHERE idCursoMateria = NEW.idCursoMateria;
    IF v_estado_curso = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se puede inscribir en un curso inactivo.';
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_disable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_store');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_estudiantemateria_before_insert');

        // Restore original sp_cursos_store without active validations
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
};
