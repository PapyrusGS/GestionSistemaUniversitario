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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_filtros');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_reporte_semestre');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_estadisticas_aprobacion');

        // 1. sp_docente_filtros
        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_filtros(IN p_docente_id INT)
BEGIN
    -- Obtener periodos del docente (incluir todos, activos e inactivos)
    SELECT DISTINCT 'periodo' AS tipo, CAST(p.idPeriodo AS CHAR) AS id, p.nombre AS valor
    FROM periodos p
    INNER JOIN cursos_materias cm ON cm.idPeriodo = p.idPeriodo
    WHERE cm.idDocente = p_docente_id
    
    UNION ALL
    
    -- Obtener materias del docente
    SELECT DISTINCT 'materia' AS tipo, cm.idMateria COLLATE utf8mb4_unicode_ci AS id, m.nombre AS valor
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria = cm.idMateria
    WHERE cm.idDocente = p_docente_id AND cm.estado = 1;
END
SQL);

        // 2. sp_docente_reporte_semestre
        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_reporte_semestre(
    IN p_docente_id INT,
    IN p_periodo_id INT
)
BEGIN
    SELECT 
        em.idInscripcion,
        em.idEstudiante,
        CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2) AS nombreCompleto,
        m.nombre AS materia_nombre,
        n.nota AS nota,
        CASE 
            WHEN n.nota IS NULL THEN 'SIN REGISTRO'
            WHEN n.nota >= 51 THEN 'APROBADO'
            ELSE 'REPROBADO'
        END AS estadoAcademico,
        
        -- Totales del semestre inline
        (SELECT COUNT(DISTINCT em_sub.idInscripcion)
         FROM estudiantemateria em_sub
         INNER JOIN cursos_materias cm_sub ON cm_sub.idCursoMateria = em_sub.idCursoMateria
         WHERE cm_sub.idDocente = p_docente_id AND cm_sub.idPeriodo = p_periodo_id AND em_sub.estado = 1) AS total_estudiantes,
         
        (SELECT COUNT(n_sub.idNota)
         FROM notas n_sub
         INNER JOIN estudiantemateria em_sub2 ON em_sub2.idInscripcion = n_sub.idInscripcion
         INNER JOIN cursos_materias cm_sub2 ON cm_sub2.idCursoMateria = em_sub2.idCursoMateria
         WHERE cm_sub2.idDocente = p_docente_id AND cm_sub2.idPeriodo = p_periodo_id AND n_sub.nota >= 51) AS total_aprobados,
         
        (SELECT COUNT(n_sub2.idNota)
         FROM notas n_sub2
         INNER JOIN estudiantemateria em_sub3 ON em_sub3.idInscripcion = n_sub2.idInscripcion
         INNER JOIN cursos_materias cm_sub3 ON cm_sub3.idCursoMateria = em_sub3.idCursoMateria
         WHERE cm_sub3.idDocente = p_docente_id AND cm_sub3.idPeriodo = p_periodo_id AND n_sub2.nota < 51) AS total_reprobados,
         
        (SELECT ROUND(COALESCE(AVG(n_sub3.nota), 0), 2)
         FROM notas n_sub3
         INNER JOIN estudiantemateria em_sub4 ON em_sub4.idInscripcion = n_sub3.idInscripcion
         INNER JOIN cursos_materias cm_sub4 ON cm_sub4.idCursoMateria = em_sub4.idCursoMateria
         WHERE cm_sub4.idDocente = p_docente_id AND cm_sub4.idPeriodo = p_periodo_id) AS promedio_general
         
    FROM estudiantemateria em
    INNER JOIN estudiante e ON e.idEstudiante = em.idEstudiante
    INNER JOIN usuarios u ON u.idUsuario = e.idUsuario
    INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
    INNER JOIN materias m ON m.idMateria = cm.idMateria
    LEFT JOIN notas n ON n.idInscripcion = em.idInscripcion
    WHERE cm.idDocente = p_docente_id 
      AND cm.idPeriodo = p_periodo_id
      AND em.estado = 1
    ORDER BY m.nombre ASC, u.apellido1 ASC, u.nombre1 ASC;
END
SQL);

        // 3. sp_docente_estadisticas_aprobacion
        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_estadisticas_aprobacion(
    IN p_docente_id INT,
    IN p_periodo_id INT,
    IN p_materia_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    SELECT 
        m.idMateria,
        m.nombre AS materia_nombre,
        p.idPeriodo,
        p.nombre AS periodo_nombre,
        COALESCE(SUM(CASE WHEN n.nota >= 51 THEN 1 ELSE 0 END), 0) AS aprobados,
        COALESCE(SUM(CASE WHEN n.nota < 51 THEN 1 ELSE 0 END), 0) AS reprobados,
        COUNT(n.idNota) AS total_notas,
        ROUND(
            COALESCE(
                (SUM(CASE WHEN n.nota >= 51 THEN 1 ELSE 0 END) / NULLIF(COUNT(n.idNota), 0)) * 100, 
                0
            ), 
            2
        ) AS porcentaje_aprobacion,
        ROUND(COALESCE(AVG(n.nota), 0), 2) AS promedio_general
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria = cm.idMateria
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    LEFT JOIN estudiantemateria em ON em.idCursoMateria = cm.idCursoMateria AND em.estado = 1
    LEFT JOIN notas n ON n.idInscripcion = em.idInscripcion
    WHERE cm.idDocente = p_docente_id
      AND (p_periodo_id IS NULL OR p_periodo_id = 0 OR cm.idPeriodo = p_periodo_id)
      AND (p_materia_id IS NULL OR p_materia_id = '' OR cm.idMateria = p_materia_id)
    GROUP BY m.idMateria, m.nombre, p.idPeriodo, p.nombre, p.fechaInicioSemestre
    ORDER BY p.fechaInicioSemestre DESC, m.nombre ASC;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_filtros');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_reporte_semestre');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_estadisticas_aprobacion');
    }
};
