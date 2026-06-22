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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_nota');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_registrar_nota(
    IN p_docente_id INT,
    IN p_estudiante_materia_id INT,
    IN p_nota DECIMAL(5,2)
)
BEGIN
    DECLARE v_idCursoMateria INT;
    DECLARE v_idDocente INT;
    DECLARE v_exists_inscripcion INT;
    DECLARE v_exists_nota INT;

    -- Validar rango de nota
    IF p_nota < 0.00 OR p_nota > 100.00 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La nota debe estar entre 0 y 100.';
    END IF;

    -- Validar existencia del estudiante-materia (inscripcion)
    SELECT COUNT(*), MAX(idCursoMateria) INTO v_exists_inscripcion, v_idCursoMateria
    FROM estudiantemateria
    WHERE idInscripcion = p_estudiante_materia_id;

    IF v_exists_inscripcion = 0 OR v_idCursoMateria IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La inscripcion no existe.';
    END IF;

    -- Validar pertenencia al docente
    SELECT COUNT(*) INTO v_idDocente
    FROM cursos_materias
    WHERE idCursoMateria = v_idCursoMateria AND idDocente = p_docente_id;

    IF v_idDocente = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante no esta inscrito en un curso asignado a este docente.';
    END IF;

    -- Validar notas duplicadas
    SELECT COUNT(*) INTO v_exists_nota
    FROM notas
    WHERE idInscripcion = p_estudiante_materia_id;

    IF v_exists_nota > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante ya tiene una nota registrada para esta materia.';
    END IF;

    -- Insertar nota
    INSERT INTO notas (
        idInscripcion,
        nota,
        fechaRegistro,
        estado,
        created_at,
        updated_at
    ) VALUES (
        p_estudiante_materia_id,
        p_nota,
        NOW(),
        1,
        NOW(),
        NOW()
    );

    -- Retornar la nota insertada
    SELECT 
        idNota,
        idInscripcion,
        nota,
        DATE_FORMAT(fechaRegistro, '%Y-%m-%d %H:%i:%s') AS fechaRegistro,
        estado
    FROM notas
    WHERE idNota = LAST_INSERT_ID();
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_registrar_nota');
    }
};
