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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_reporte_notas');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_reporte_notas(
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
        SET MESSAGE_TEXT = 'No autorizado para consultar este curso';
    END IF;

    -- 3. Retornar las calificaciones de los estudiantes inscritos en el curso
    SELECT 
        em.idInscripcion,
        CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2) AS nombreCompleto,
        n.nota AS nota,
        CASE 
            WHEN n.nota IS NULL THEN 'SIN REGISTRO'
            WHEN n.nota >= 51 THEN 'APROBADO'
            ELSE 'REPROBADO'
        END AS estadoAcademico,
        
        -- Sumario general calculado inline por ventana para facilidad de extracción en el servicio
        (SELECT COUNT(DISTINCT em_sub.idInscripcion) 
         FROM estudiantemateria em_sub 
         WHERE em_sub.idCursoMateria = p_curso_materia_id AND em_sub.estado = 1) AS total_estudiantes,
         
        (SELECT COUNT(n_sub.idNota) 
         FROM notas n_sub 
         INNER JOIN estudiantemateria em_sub2 ON em_sub2.idInscripcion = n_sub.idInscripcion
         WHERE em_sub2.idCursoMateria = p_curso_materia_id) AS total_con_nota,
         
        (SELECT ROUND(COALESCE(AVG(n_sub2.nota), 0), 2) 
         FROM notas n_sub2
         INNER JOIN estudiantemateria em_sub3 ON em_sub3.idInscripcion = n_sub2.idInscripcion
         WHERE em_sub3.idCursoMateria = p_curso_materia_id) AS promedio_general
         
    FROM estudiantemateria em
    INNER JOIN estudiante e ON e.idEstudiante = em.idEstudiante
    INNER JOIN usuarios u ON u.idUsuario = e.idUsuario
    LEFT JOIN notas n ON n.idInscripcion = em.idInscripcion
    WHERE em.idCursoMateria = p_curso_materia_id AND em.estado = 1
    ORDER BY u.apellido1 ASC, u.apellido2 ASC, u.nombre1 ASC;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_reporte_notas');
    }
};
