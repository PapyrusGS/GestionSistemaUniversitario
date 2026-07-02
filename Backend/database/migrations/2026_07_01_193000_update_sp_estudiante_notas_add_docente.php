<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_notas');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_notas(IN p_idEstudiante BIGINT UNSIGNED)
BEGIN
    SELECT
        m.idMateria,
        m.nombre AS materia,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        p.nombre AS periodo,
        n.nota,
        n.fechaRegistro,
        TRIM(CONCAT(d.nombre1, ' ', COALESCE(d.apellido1, ''))) AS docente
    FROM notas n
    INNER JOIN estudiantemateria em ON em.idInscripcion = n.idInscripcion
    INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    INNER JOIN usuarios d ON d.idUsuario = cm.idDocente
    WHERE em.idEstudiante = p_idEstudiante
      AND n.estado = 1
    ORDER BY CAST(m.semestre AS UNSIGNED), m.nombre;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_notas');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_notas(IN p_idEstudiante BIGINT UNSIGNED)
BEGIN
    SELECT
        m.idMateria,
        m.nombre AS materia,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        p.nombre AS periodo,
        n.nota,
        n.fechaRegistro
    FROM notas n
    INNER JOIN estudiantemateria em ON em.idInscripcion = n.idInscripcion
    INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    WHERE em.idEstudiante = p_idEstudiante
      AND n.estado = 1
    ORDER BY CAST(m.semestre AS UNSIGNED), m.nombre;
END
SQL);
    }
};
