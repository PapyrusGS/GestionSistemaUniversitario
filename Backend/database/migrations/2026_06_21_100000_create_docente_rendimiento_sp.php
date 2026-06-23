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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_rendimiento');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_rendimiento(
    IN p_docente_id INT,
    IN p_curso_materia_id INT
)
BEGIN
    -- 1. Validar que el curso exista
    IF NOT EXISTS (
        SELECT 1 
        FROM cursos_materias 
        WHERE idCursoMateria = p_curso_materia_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Curso no encontrado';
    END IF;

    -- 2. Validar que el curso pertenezca al docente autenticado
    IF NOT EXISTS (
        SELECT 1 
        FROM cursos_materias 
        WHERE idCursoMateria = p_curso_materia_id AND idDocente = p_docente_id
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El curso no pertenece al docente';
    END IF;

    -- 3. Retornar estadísticas calculadas
    SELECT 
        COALESCE(SUM(CASE WHEN n.nota >= 51 THEN 1 ELSE 0 END), 0) AS aprobados,
        COALESCE(SUM(CASE WHEN n.nota < 51 THEN 1 ELSE 0 END), 0) AS reprobados,
        ROUND(COALESCE(AVG(n.nota), 0), 2) AS promedio,
        COUNT(n.idNota) AS total_notas
    FROM estudiantemateria em
    INNER JOIN notas n ON n.idInscripcion = em.idInscripcion
    WHERE em.idCursoMateria = p_curso_materia_id;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_rendimiento');
    }
};
