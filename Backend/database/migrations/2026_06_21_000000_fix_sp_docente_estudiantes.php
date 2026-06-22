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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_estudiantes');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_estudiantes(IN p_idCursoMateria INT)
BEGIN
    SELECT 
        em.idInscripcion,
        em.idEstudiante,
        u.ci,
        u.nombre1,
        u.nombre2,
        u.apellido1,
        u.apellido2,
        u.correo,
        DATE_FORMAT(em.fecha, '%Y-%m-%d') AS fecha_inscripcion
    FROM estudiantemateria em
    INNER JOIN estudiante e ON e.idEstudiante = em.idEstudiante
    INNER JOIN usuarios u ON u.idUsuario = e.idUsuario
    WHERE em.idCursoMateria = p_idCursoMateria AND em.estado = 1;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_estudiantes');
        
        // Revert to original using string 'Inscrito' just in case
        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_estudiantes(IN p_idCursoMateria INT)
BEGIN
    SELECT 
        em.idInscripcion,
        em.idEstudiante,
        u.ci,
        u.nombre1,
        u.nombre2,
        u.apellido1,
        u.apellido2,
        u.correo,
        DATE_FORMAT(em.fecha, '%Y-%m-%d') AS fecha_inscripcion
    FROM estudiantemateria em
    INNER JOIN estudiante e ON e.idEstudiante = em.idEstudiante
    INNER JOIN usuarios u ON u.idUsuario = e.idUsuario
    WHERE em.idCursoMateria = p_idCursoMateria AND em.estado = 'Inscrito';
END
SQL);
    }
};
