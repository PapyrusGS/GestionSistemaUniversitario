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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docentes_disponibles');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docentes_disponibles(
    IN p_idPeriodo BIGINT UNSIGNED,
    IN p_horario1 INT,
    IN p_horario2 INT,
    IN p_horario3 INT
)
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
      AND u.idUsuario NOT IN (
          SELECT DISTINCT cm.idDocente 
          FROM cursos_materias cm
          INNER JOIN horariocurso hc ON hc.idCursoMateria = cm.idCursoMateria
          WHERE cm.idPeriodo = p_idPeriodo
            AND cm.estado = 1
            AND (
                hc.idHorario = p_horario1
                OR (p_horario2 IS NOT NULL AND hc.idHorario = p_horario2)
                OR (p_horario3 IS NOT NULL AND hc.idHorario = p_horario3)
            )
      )
    ORDER BY u.apellido1, u.apellido2, u.nombre1, u.nombre2;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docentes_disponibles');
    }
};
