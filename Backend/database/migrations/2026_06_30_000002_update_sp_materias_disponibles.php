<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_materias_disponibles');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_materias_disponibles(
    IN p_idEstudiante BIGINT UNSIGNED,
    IN p_idCarrera BIGINT UNSIGNED
)
BEGIN
    DECLARE v_semestre_actual INT DEFAULT 1;

    SELECT COALESCE(MAX(m.semestre), 0) + 1 INTO v_semestre_actual
    FROM materias m
    WHERE m.idMateria IN (
        SELECT cm_ap.idMateria
        FROM notas n_ap
        INNER JOIN estudiantemateria em_ap ON em_ap.idInscripcion = n_ap.idInscripcion
        INNER JOIN cursos_materias cm_ap ON cm_ap.idCursoMateria = em_ap.idCursoMateria
        WHERE em_ap.idEstudiante = p_idEstudiante
          AND n_ap.estado = 1
          AND n_ap.nota >= 51
    );

    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idPeriodo,
        c.capacidad AS maxInscritos,
        cm.fechaInicio,
        cm.fechaFin,
        m.idCarrera,
        m.nombre AS materia,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        m.idMateriaPrevia,
        mp.nombre AS prerrequisito,
        p.nombre AS periodo,
        TRIM(CONCAT(d.nombre1, ' ', COALESCE(d.apellido1, ''))) AS docente,
        COUNT(em.idInscripcion) AS inscritos
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN cursos c ON c.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    INNER JOIN usuarios d ON d.idUsuario = cm.idDocente
    LEFT JOIN materias mp ON mp.idMateria COLLATE utf8mb4_unicode_ci = m.idMateriaPrevia COLLATE utf8mb4_unicode_ci
    LEFT JOIN estudiantemateria em ON em.idCursoMateria = cm.idCursoMateria AND em.estado = 1
    WHERE cm.estado = 1
      AND m.estado = 1
      AND c.estado = 1
      AND m.idCarrera = p_idCarrera
      AND m.idMateria NOT IN (
          SELECT cm_ap.idMateria
          FROM notas n_ap
          INNER JOIN estudiantemateria em_ap ON em_ap.idInscripcion = n_ap.idInscripcion
          INNER JOIN cursos_materias cm_ap ON cm_ap.idCursoMateria = em_ap.idCursoMateria
          WHERE em_ap.idEstudiante = p_idEstudiante
            AND n_ap.estado = 1
            AND n_ap.nota >= 51
      )
      AND cm.idCursoMateria NOT IN (
          SELECT em_ins.idCursoMateria
          FROM estudiantemateria em_ins
          WHERE em_ins.idEstudiante = p_idEstudiante
            AND em_ins.estado = 1
      )
      AND (
          v_semestre_actual >= 8
          OR CAST(m.semestre AS UNSIGNED) <= v_semestre_actual
      )
      AND (
          m.idMateriaPrevia IS NULL
          OR m.idMateriaPrevia IN (
              SELECT cm_pr.idMateria
              FROM notas n_pr
              INNER JOIN estudiantemateria em_pr ON em_pr.idInscripcion = n_pr.idInscripcion
              INNER JOIN cursos_materias cm_pr ON cm_pr.idCursoMateria = em_pr.idCursoMateria
              WHERE em_pr.idEstudiante = p_idEstudiante
                AND n_pr.estado = 1
                AND n_pr.nota >= 51
          )
      )
    GROUP BY
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idPeriodo,
        c.capacidad,
        cm.fechaInicio,
        cm.fechaFin,
        m.idCarrera,
        m.nombre,
        m.semestre,
        m.idMateriaPrevia,
        mp.nombre,
        p.nombre,
        d.nombre1,
        d.apellido1
    ORDER BY CAST(m.semestre AS UNSIGNED), m.nombre;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_materias_disponibles');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_materias_disponibles(
    IN p_idEstudiante BIGINT UNSIGNED,
    IN p_idCarrera BIGINT UNSIGNED
)
BEGIN
    DECLARE v_semestre_actual INT DEFAULT 1;

    SELECT COALESCE(MAX(m.semestre), 0) + 1 INTO v_semestre_actual
    FROM materias m
    WHERE m.idMateria IN (
        SELECT cm_ap.idMateria
        FROM notas n_ap
        INNER JOIN estudiantemateria em_ap ON em_ap.idInscripcion = n_ap.idInscripcion
        INNER JOIN cursos_materias cm_ap ON cm_ap.idCursoMateria = em_ap.idCursoMateria
        WHERE em_ap.idEstudiante = p_idEstudiante
          AND n_ap.estado = 1
          AND n_ap.nota >= 51
    );

    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idPeriodo,
        c.capacidad AS maxInscritos,
        cm.fechaInicio,
        cm.fechaFin,
        m.idCarrera,
        m.nombre AS materia,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        m.idMateriaPrevia,
        mp.nombre AS prerrequisito,
        p.nombre AS periodo,
        TRIM(CONCAT(d.nombre1, ' ', COALESCE(d.apellido1, ''))) AS docente,
        COUNT(em.idInscripcion) AS inscritos
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN cursos c ON c.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    INNER JOIN usuarios d ON d.idUsuario = cm.idDocente
    LEFT JOIN materias mp ON mp.idMateria COLLATE utf8mb4_unicode_ci = m.idMateriaPrevia COLLATE utf8mb4_unicode_ci
    LEFT JOIN estudiantemateria em ON em.idCursoMateria = cm.idCursoMateria AND em.estado = 1
    WHERE cm.estado = 1
      AND m.estado = 1
      AND c.estado = 1
      AND p.estado = 1
      AND m.idCarrera = p_idCarrera
      AND m.idMateria NOT IN (
          SELECT cm_ap.idMateria
          FROM notas n_ap
          INNER JOIN estudiantemateria em_ap ON em_ap.idInscripcion = n_ap.idInscripcion
          INNER JOIN cursos_materias cm_ap ON cm_ap.idCursoMateria = em_ap.idCursoMateria
          WHERE em_ap.idEstudiante = p_idEstudiante
            AND n_ap.estado = 1
            AND n_ap.nota >= 51
      )
      AND cm.idCursoMateria NOT IN (
          SELECT em_ins.idCursoMateria
          FROM estudiantemateria em_ins
          WHERE em_ins.idEstudiante = p_idEstudiante
            AND em_ins.estado = 1
      )
      AND (
          v_semestre_actual >= 8
          OR CAST(m.semestre AS UNSIGNED) <= v_semestre_actual
      )
      AND (
          m.idMateriaPrevia IS NULL
          OR m.idMateriaPrevia IN (
              SELECT cm_pr.idMateria
              FROM notas n_pr
              INNER JOIN estudiantemateria em_pr ON em_pr.idInscripcion = n_pr.idInscripcion
              INNER JOIN cursos_materias cm_pr ON cm_pr.idCursoMateria = em_pr.idCursoMateria
              WHERE em_pr.idEstudiante = p_idEstudiante
                AND n_pr.estado = 1
                AND n_pr.nota >= 51
          )
      )
    GROUP BY
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idPeriodo,
        c.capacidad,
        cm.fechaInicio,
        cm.fechaFin,
        m.idCarrera,
        m.nombre,
        m.semestre,
        m.idMateriaPrevia,
        mp.nombre,
        p.nombre,
        d.nombre1,
        d.apellido1
    ORDER BY CAST(m.semestre AS UNSIGNED), m.nombre;
END
SQL);
    }
};
