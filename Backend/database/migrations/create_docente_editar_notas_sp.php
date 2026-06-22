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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_editar_nota');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_editar_nota(
    IN p_docente_id INT,
    IN p_nota_id INT,
    IN p_nueva_nota DECIMAL(5,2)
)
BEGIN
    DECLARE v_idInscripcion INT;
    DECLARE v_idCursoMateria INT;
    DECLARE v_idDocente INT;
    DECLARE v_exists_nota INT;

    -- 1. Validar rango de la nueva nota
    IF p_nueva_nota < 0.00 OR p_nueva_nota > 100.00 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Nota fuera de rango';
    END IF;

    -- 2. Validar existencia de la nota
    SELECT COUNT(*), MAX(idInscripcion) INTO v_exists_nota, v_idInscripcion
    FROM notas
    WHERE idNota = p_nota_id;

    IF v_exists_nota = 0 OR v_idInscripcion IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Nota no encontrada';
    END IF;

    -- 3. Validar pertenencia al docente autenticado
    SELECT MAX(idCursoMateria) INTO v_idCursoMateria
    FROM estudiantemateria
    WHERE idInscripcion = v_idInscripcion;

    SELECT COUNT(*) INTO v_idDocente
    FROM cursos_materias
    WHERE idCursoMateria = v_idCursoMateria AND idDocente = p_docente_id;

    IF v_idDocente = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No autorizado para editar esta nota';
    END IF;

    -- 4. Actualizar TNota
    UPDATE notas
    SET 
        nota = p_nueva_nota,
        fechaActualizacion = NOW(),
        updated_at = NOW()
    WHERE idNota = p_nota_id;

    -- Retornar nota actualizada
    SELECT 
        idNota,
        idInscripcion,
        nota,
        DATE_FORMAT(fechaRegistro, '%Y-%m-%d %H:%i:%s') AS fechaRegistro,
        estado
    FROM notas
    WHERE idNota = p_nota_id;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_editar_nota');
    }
};
