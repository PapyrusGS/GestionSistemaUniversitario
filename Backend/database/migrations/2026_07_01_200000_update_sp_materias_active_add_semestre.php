<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_active');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_active()
BEGIN
    SELECT
        m.idMateria,
        m.idCarrera,
        m.nombre,
        m.semestre
    FROM materias m
    WHERE m.estado = 1
    ORDER BY m.nombre;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_active');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_active()
BEGIN
    SELECT
        m.idMateria,
        m.idCarrera,
        m.nombre
    FROM materias m
    WHERE m.estado = 1
    ORDER BY m.nombre;
END
SQL);
    }
};
