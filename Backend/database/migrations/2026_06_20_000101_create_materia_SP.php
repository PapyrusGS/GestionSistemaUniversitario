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

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_list(
    IN p_idCarrera BIGINT UNSIGNED,
    IN p_busqueda VARCHAR(255),
    IN p_semestre INT
)
BEGIN
    DECLARE v_busqueda VARCHAR(255);

    SET v_busqueda = NULLIF(TRIM(p_busqueda), '');

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
    LEFT JOIN materias p
        ON p.idMateria COLLATE utf8mb4_unicode_ci = m.idMateriaPrevia COLLATE utf8mb4_unicode_ci
    WHERE (p_idCarrera IS NULL OR m.idCarrera = p_idCarrera)
      AND (p_semestre IS NULL OR m.semestre = p_semestre)
      AND (
            v_busqueda IS NULL
            OR m.idMateria COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', v_busqueda, '%')
          )
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
    LEFT JOIN materias p
        ON p.idMateria COLLATE utf8mb4_unicode_ci = m.idMateriaPrevia COLLATE utf8mb4_unicode_ci
    WHERE m.idMateria COLLATE utf8mb4_unicode_ci = p_idMateria COLLATE utf8mb4_unicode_ci
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
    DECLARE v_idMateriaPrevia VARCHAR(100);

    SET v_idMateriaPrevia = CASE
        WHEN p_idMateriaPrevia IS NULL OR CHAR_LENGTH(TRIM(p_idMateriaPrevia)) = 0 THEN NULL
        ELSE p_idMateriaPrevia
    END;

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
        v_idMateriaPrevia,
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
    DECLARE v_idMateriaPrevia VARCHAR(100);

    SET v_idMateriaPrevia = CASE
        WHEN p_idMateriaPrevia IS NULL OR CHAR_LENGTH(TRIM(p_idMateriaPrevia)) = 0 THEN NULL
        ELSE p_idMateriaPrevia
    END;

    UPDATE materias
    SET
        idCarrera = p_idCarrera,
        idMateriaPrevia = v_idMateriaPrevia,
        nombre = p_nombre,
        semestre = p_semestre,
        updated_at = NOW()
    WHERE idMateria COLLATE utf8mb4_unicode_ci = p_idMateria COLLATE utf8mb4_unicode_ci;

    CALL sp_materias_find(p_idMateria);
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_materias_disable(IN p_idMateria VARCHAR(100))
BEGIN
    UPDATE materias
    SET estado = 0,
        updated_at = NOW()
    WHERE idMateria COLLATE utf8mb4_unicode_ci = p_idMateria COLLATE utf8mb4_unicode_ci;

    CALL sp_materias_find(p_idMateria);
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_disable');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_store');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_find');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_active');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_materias_list');
    }
};
