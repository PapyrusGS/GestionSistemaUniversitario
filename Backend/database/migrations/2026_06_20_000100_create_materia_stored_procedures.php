<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_disable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_carreras_disable');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_list()
BEGIN
    SELECT
        m.idMateria,
        m.idCarrera,
        m.idMateriaPrevia,
        m.nombre,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        CAST(m.estado AS UNSIGNED) AS estado,
        DATE_FORMAT(m.fechaRegistro, '%Y-%m-%d %H:%i') AS fechaRegistro,
        c.nombre AS carrera,
        p.nombre AS prerrequisito
    FROM materias m
    INNER JOIN carreras c ON c.idCarrera = m.idCarrera
    LEFT JOIN materias p ON p.idMateria = m.idMateriaPrevia
    ORDER BY m.estado DESC, m.idCarrera, m.semestre, m.nombre;
END
SQL);

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

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_find(IN p_idMateria VARCHAR(100))
BEGIN
    SELECT
        m.idMateria,
        m.idCarrera,
        m.idMateriaPrevia,
        m.nombre,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        CAST(m.estado AS UNSIGNED) AS estado,
        DATE_FORMAT(m.fechaRegistro, '%Y-%m-%d %H:%i') AS fechaRegistro,
        c.nombre AS carrera,
        p.nombre AS prerrequisito
    FROM materias m
    INNER JOIN carreras c ON c.idCarrera = m.idCarrera
    LEFT JOIN materias p ON p.idMateria = m.idMateriaPrevia
    WHERE m.idMateria = p_idMateria
    LIMIT 1;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_store(
    IN p_idMateria VARCHAR(100),
    IN p_idCarrera BIGINT UNSIGNED,
    IN p_idMateriaPrevia VARCHAR(100),
    IN p_nombre VARCHAR(255),
    IN p_semestre INT
)
BEGIN
    INSERT INTO materias (
        idMateria,
        idCarrera,
        idMateriaPrevia,
        nombre,
        semestre,
        fechaRegistro,
        estado,
        created_at,
        updated_at
    ) VALUES (
        p_idMateria,
        p_idCarrera,
        NULLIF(p_idMateriaPrevia, ''),
        p_nombre,
        p_semestre,
        NOW(),
        1,
        NOW(),
        NOW()
    );

    CALL sp_materias_find(p_idMateria);
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_update(
    IN p_idMateria VARCHAR(100),
    IN p_idCarrera BIGINT UNSIGNED,
    IN p_idMateriaPrevia VARCHAR(100),
    IN p_nombre VARCHAR(255),
    IN p_semestre INT
)
BEGIN
    UPDATE materias
    SET
        idCarrera = p_idCarrera,
        idMateriaPrevia = NULLIF(p_idMateriaPrevia, ''),
        nombre = p_nombre,
        semestre = p_semestre,
        updated_at = NOW()
    WHERE idMateria = p_idMateria;

    CALL sp_materias_find(p_idMateria);
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_disable(IN p_idMateria VARCHAR(100))
BEGIN
    UPDATE materias
    SET estado = 0,
        updated_at = NOW()
    WHERE idMateria = p_idMateria;

    CALL sp_materias_find(p_idMateria);
END
SQL);

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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_disable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_list');
    }
};
