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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_cursos');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_estudiantes');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_notas');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_cursos(IN p_docente_id INT)
BEGIN
    SELECT 
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        m.nombre AS materia_nombre,
        DATE_FORMAT(cm.fechaInicio, '%Y-%m-%d') AS fechaInicio,
        DATE_FORMAT(cm.fechaFin, '%Y-%m-%d') AS fechaFin,
        COALESCE(
            (SELECT GROUP_CONCAT(DISTINCT CONCAT('Dia ', h.diaSemana, ' ', TIME_FORMAT(h.horaInicio, '%H:%i'), '-', TIME_FORMAT(h.horaFin, '%H:%i')) SEPARATOR ', ')
             FROM horariocurso hc
             INNER JOIN horarios h ON h.idHorario = hc.idHorario
             WHERE hc.idCursoMateria = cm.idCursoMateria),
            'Sin Horario'
        ) AS turno_nombre
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria = cm.idMateria
    WHERE cm.idDocente = p_docente_id AND cm.estado = 1;
END
SQL);

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

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docente_notas(IN p_idCursoMateria INT)
BEGIN
    SELECT 
        n.idNota,
        n.idInscripcion,
        n.nota,
        DATE_FORMAT(n.fechaRegistro, '%Y-%m-%d %H:%i:%s') AS fechaRegistro,
        n.estado,
        CONCAT_WS(' ', u.nombre1, u.nombre2, u.apellido1, u.apellido2) AS estudiante_nombre,
        m.nombre AS materia_nombre
    FROM notas n
    INNER JOIN estudiantemateria em ON em.idInscripcion = n.idInscripcion
    INNER JOIN estudiante e ON e.idEstudiante = em.idEstudiante
    INNER JOIN usuarios u ON u.idUsuario = e.idUsuario
    INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
    INNER JOIN materias m ON m.idMateria = cm.idMateria
    WHERE em.idCursoMateria = p_idCursoMateria;
END
SQL);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_cursos');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_estudiantes');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docente_notas');
    }
};
