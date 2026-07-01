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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_disable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_enable');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_fisicos_list()
BEGIN
    SELECT 
        idCurso,
        capacidad,
        CAST(estado AS UNSIGNED) AS estado,
        DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') AS fechaRegistro
    FROM cursos
    ORDER BY idCurso;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_fisicos_find(IN p_idCurso VARCHAR(100))
BEGIN
    SELECT 
        idCurso,
        capacidad,
        CAST(estado AS UNSIGNED) AS estado,
        DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') AS fechaRegistro
    FROM cursos
    WHERE idCurso COLLATE utf8mb4_unicode_ci = p_idCurso COLLATE utf8mb4_unicode_ci;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_fisicos_store(
    IN p_idCurso VARCHAR(100),
    IN p_capacidad INT
)
BEGIN
    INSERT INTO cursos (
        idCurso,
        capacidad,
        estado,
        created_at,
        updated_at
    ) VALUES (
        p_idCurso,
        p_capacidad,
        1,
        NOW(),
        NOW()
    );
    
    CALL sp_cursos_fisicos_find(p_idCurso);
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_fisicos_update(
    IN p_idCurso VARCHAR(100),
    IN p_capacidad INT
)
BEGIN
    UPDATE cursos 
    SET 
        capacidad = p_capacidad,
        updated_at = NOW()
    WHERE idCurso COLLATE utf8mb4_unicode_ci = p_idCurso COLLATE utf8mb4_unicode_ci;
    
    CALL sp_cursos_fisicos_find(p_idCurso);
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_fisicos_disable(IN p_idCurso VARCHAR(100))
BEGIN
    UPDATE cursos
    SET 
        estado = 0,
        updated_at = NOW()
    WHERE idCurso COLLATE utf8mb4_unicode_ci = p_idCurso COLLATE utf8mb4_unicode_ci;
    
    CALL sp_cursos_fisicos_find(p_idCurso);
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_cursos_fisicos_enable(IN p_idCurso VARCHAR(100))
BEGIN
    UPDATE cursos
    SET 
        estado = 1,
        updated_at = NOW()
    WHERE idCurso COLLATE utf8mb4_unicode_ci = p_idCurso COLLATE utf8mb4_unicode_ci;
    
    CALL sp_cursos_fisicos_find(p_idCurso);
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_enable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_disable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cursos_fisicos_list');
    }
};
