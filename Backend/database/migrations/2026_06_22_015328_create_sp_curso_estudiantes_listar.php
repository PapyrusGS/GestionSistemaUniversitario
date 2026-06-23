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
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_curso_estudiantes_listar;");

        DB::unprepared("
            CREATE PROCEDURE sp_curso_estudiantes_listar(IN p_idCursoMateria BIGINT UNSIGNED)
            BEGIN
                SELECT 
                    em.idInscripcion,
                    e.idEstudiante AS id_estudiante,
                    u.ci,
                    u.nombre1,
                    u.nombre2,
                    u.apellido1,
                    u.apellido2,
                    u.correo,
                    DATE_FORMAT(em.created_at, '%Y-%m-%d') AS fecha_inscripcion
                FROM estudiantemateria em
                INNER JOIN estudiante e ON em.idEstudiante = e.idEstudiante
                INNER JOIN usuarios u ON e.idUsuario = u.idUsuario
                WHERE em.idCursoMateria = p_idCursoMateria AND em.estado = 1;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_curso_estudiantes_listar;");
    }
};