<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
 
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_disable');

        DB::unprepared(<<<SQL

CREATE PROCEDURE sp_carreras_list()
BEGIN
    SELECT
        c.idCarrera,
        c.nombre,
        c.descripcion,
        CAST(c.estado AS UNSIGNED) AS estado,
        DATE_FORMAT(c.fechaRegistro, '%Y-%m-%d %H:%i') AS fechaRegistro
    FROM carreras c
    ORDER BY c.estado DESC, c.nombre;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_carreras_active()
BEGIN
    SELECT
        c.idCarrera,
        c.nombre
    FROM carreras c
    WHERE c.estado = 1
    ORDER BY c.nombre;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_carreras_find(IN p_idCarrera BIGINT UNSIGNED)
BEGIN
    SELECT
        c.idCarrera,
        c.nombre,
        c.descripcion,
        CAST(c.estado AS UNSIGNED) AS estado,
        DATE_FORMAT(c.fechaRegistro, '%Y-%m-%d %H:%i') AS fechaRegistro
    FROM carreras c
    WHERE c.idCarrera = p_idCarrera
    LIMIT 1;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_carreras_store(
    IN p_nombre VARCHAR(255),
    IN p_descripcion VARCHAR(255)
)
BEGIN
    INSERT INTO carreras (
        nombre,
        descripcion,
        estado,
        fechaRegistro,
        created_at,
        updated_at
    ) VALUES (
        p_nombre,
        p_descripcion,
        1,
        NOW(),
        NOW(),
        NOW()
    );

    CALL sp_carreras_find(LAST_INSERT_ID());
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_carreras_update(
    IN p_idCarrera BIGINT UNSIGNED,
    IN p_nombre VARCHAR(255),
    IN p_descripcion VARCHAR(255)
)
BEGIN
    UPDATE carreras
    SET
        nombre = p_nombre,
        descripcion = p_descripcion,
        updated_at = NOW()
    WHERE idCarrera = p_idCarrera;

    CALL sp_carreras_find(p_idCarrera);
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_carreras_disable(IN p_idCarrera BIGINT UNSIGNED)
BEGIN
    UPDATE carreras
    SET estado = 0,
        updated_at = NOW()
    WHERE idCarrera = p_idCarrera;

    CALL sp_carreras_find(p_idCarrera);
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_disable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_list');

    }
};
