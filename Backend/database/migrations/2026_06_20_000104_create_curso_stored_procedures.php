<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_horarios_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_periodos_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docentes_active');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_horarios_active()
BEGIN
    SELECT
        h.idHorario,
        h.diaSemana,
        h.horaInicio,
        h.horaFin,
        CONCAT('Dia ', h.diaSemana, ' - ', TIME_FORMAT(h.horaInicio, '%H:%i'), ' a ', TIME_FORMAT(h.horaFin, '%H:%i')) AS descripcion
    FROM horarios h
    ORDER BY h.diaSemana, h.horaInicio;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_periodos_active()
BEGIN
    SELECT
        p.idPeriodo,
        p.nombre,
        p.fechaInicioSemestre,
        p.fechaFinSemestre
    FROM periodos p
    WHERE p.estado = 1
    ORDER BY p.fechaInicioSemestre DESC, p.idPeriodo DESC;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_docentes_active()
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
    ORDER BY u.apellido1, u.apellido2, u.nombre1, u.nombre2;
END
SQL);

    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_docentes_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_periodos_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_horarios_active');
    }
};
