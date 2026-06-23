<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_profile');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_career');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_materias_disponibles');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_inscripciones');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_notas');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_historial');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_horario_texto');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_aprobadas');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_inscritas_ids');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_inscribir');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_horario_cruce');

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_profile(IN p_idUsuario BIGINT UNSIGNED)
BEGIN
    SELECT
        e.idEstudiante,
        e.idUsuario,
        u.nombre1,
        u.nombre2,
        u.apellido1,
        u.apellido2,
        u.correo,
        u.ci,
        TRIM(CONCAT(
            u.nombre1, ' ', COALESCE(u.nombre2, ''),
            ' ', u.apellido1, ' ', COALESCE(u.apellido2, '')
        )) AS nombreCompleto
    FROM estudiante e
    INNER JOIN usuarios u ON u.idUsuario = e.idUsuario
    WHERE e.idUsuario = p_idUsuario
    LIMIT 1;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_career(IN p_idEstudiante BIGINT UNSIGNED)
BEGIN
    SELECT
        ec.idEstudianteCarrera,
        ec.idEstudiante,
        ec.idCarrera,
        CAST(ec.estado AS UNSIGNED) AS estado,
        c.nombre AS carrera
    FROM estudiante_carrera ec
    INNER JOIN carreras c ON c.idCarrera = ec.idCarrera
    WHERE ec.idEstudiante = p_idEstudiante
      AND ec.estado = 1
    LIMIT 1;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_materias_disponibles(IN p_idCarrera BIGINT UNSIGNED)
BEGIN
    SELECT
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idPeriodo,
        c.capacidad AS maxInscritos,
        cm.fechaInicio,
        cm.fechaFin,
        m.idCarrera,
        m.nombre AS materia,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        m.idMateriaPrevia,
        mp.nombre AS prerrequisito,
        p.nombre AS periodo,
        TRIM(CONCAT(d.nombre1, ' ', COALESCE(d.apellido1, ''))) AS docente,
        COUNT(em.idInscripcion) AS inscritos
    FROM cursos_materias cm
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN cursos c ON c.idCurso COLLATE utf8mb4_unicode_ci = cm.idCurso COLLATE utf8mb4_unicode_ci
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    INNER JOIN usuarios d ON d.idUsuario = cm.idDocente
    LEFT JOIN materias mp ON mp.idMateria COLLATE utf8mb4_unicode_ci = m.idMateriaPrevia COLLATE utf8mb4_unicode_ci
    LEFT JOIN estudiantemateria em ON em.idCursoMateria = cm.idCursoMateria AND em.estado = 1
    WHERE cm.estado = 1
      AND m.estado = 1
      AND c.estado = 1
      AND p.estado = 1
      AND m.idCarrera = p_idCarrera
    GROUP BY
        cm.idCursoMateria,
        cm.idCurso,
        cm.idMateria,
        cm.idPeriodo,
        c.capacidad,
        cm.fechaInicio,
        cm.fechaFin,
        m.idCarrera,
        m.nombre,
        m.semestre,
        m.idMateriaPrevia,
        mp.nombre,
        p.nombre,
        d.nombre1,
        d.apellido1
    ORDER BY CAST(m.semestre AS UNSIGNED), m.nombre;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_inscripciones(IN p_idEstudiante BIGINT UNSIGNED)
BEGIN
    SELECT
        em.idInscripcion,
        em.fecha,
        CAST(em.estado AS UNSIGNED) AS estado,
        cm.idCursoMateria,
        cm.idCurso,
        m.nombre AS materia,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        p.nombre AS periodo,
        TRIM(CONCAT(d.nombre1, ' ', COALESCE(d.apellido1, ''))) AS docente
    FROM estudiantemateria em
    INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    INNER JOIN usuarios d ON d.idUsuario = cm.idDocente
    WHERE em.idEstudiante = p_idEstudiante
      AND em.estado = 1
    ORDER BY CAST(m.semestre AS UNSIGNED), m.nombre;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_notas(IN p_idEstudiante BIGINT UNSIGNED)
BEGIN
    SELECT
        m.idMateria,
        m.nombre AS materia,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        p.nombre AS periodo,
        n.nota,
        n.fechaRegistro
    FROM notas n
    INNER JOIN estudiantemateria em ON em.idInscripcion = n.idInscripcion
    INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN periodos p ON p.idPeriodo = cm.idPeriodo
    WHERE em.idEstudiante = p_idEstudiante
      AND n.estado = 1
    ORDER BY CAST(m.semestre AS UNSIGNED), m.nombre;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_historial(IN p_idEstudiante BIGINT UNSIGNED, IN p_idCarrera BIGINT UNSIGNED)
BEGIN
    SELECT
        m.idMateria,
        m.nombre AS materia,
        CAST(m.semestre AS UNSIGNED) AS semestre,
        notas_sub.nota,
        notas_sub.estadoAcademico,
        notas_sub.inscrita
    FROM materias m
    LEFT JOIN (
        SELECT
            cm.idMateria,
            MAX(n.nota) AS nota,
            CASE
                WHEN MAX(n.nota) >= 51 THEN 'Aprobada'
                WHEN MAX(n.nota) IS NOT NULL THEN 'Reprobada'
                ELSE NULL
            END AS estadoAcademico,
            MAX(em.estado) AS inscrita
        FROM estudiantemateria em
        INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
        LEFT JOIN notas n ON n.idInscripcion = em.idInscripcion AND n.estado = 1
        WHERE em.idEstudiante = p_idEstudiante
        GROUP BY cm.idMateria
    ) AS notas_sub ON notas_sub.idMateria COLLATE utf8mb4_unicode_ci = m.idMateria COLLATE utf8mb4_unicode_ci
    WHERE m.idCarrera = p_idCarrera
      AND m.estado = 1
    ORDER BY CAST(m.semestre AS UNSIGNED), m.nombre;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_horario_texto(IN p_idCursoMateria BIGINT UNSIGNED)
BEGIN
    SELECT
        h.diaSemana,
        h.horaInicio,
        h.horaFin
    FROM horariocurso hc
    INNER JOIN horarios h ON h.idHorario = hc.idHorario
    WHERE hc.idCursoMateria = p_idCursoMateria
    ORDER BY h.diaSemana, h.horaInicio;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_aprobadas(IN p_idEstudiante BIGINT UNSIGNED)
BEGIN
    SELECT DISTINCT cm.idMateria
    FROM notas n
    INNER JOIN estudiantemateria em ON em.idInscripcion = n.idInscripcion
    INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
    WHERE em.idEstudiante = p_idEstudiante
      AND n.estado = 1
      AND n.nota >= 51;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_inscritas_ids(IN p_idEstudiante BIGINT UNSIGNED)
BEGIN
    SELECT idCursoMateria
    FROM estudiantemateria
    WHERE idEstudiante = p_idEstudiante
      AND estado = 1;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_horario_cruce(
    IN p_idEstudiante BIGINT UNSIGNED,
    IN p_nuevoIdCursoMateria BIGINT UNSIGNED
)
BEGIN
    SELECT DISTINCT m.nombre AS materia
    FROM estudiantemateria em
    INNER JOIN cursos_materias cm ON cm.idCursoMateria = em.idCursoMateria
    INNER JOIN materias m ON m.idMateria COLLATE utf8mb4_unicode_ci = cm.idMateria COLLATE utf8mb4_unicode_ci
    INNER JOIN horariocurso hc_exist ON hc_exist.idCursoMateria = cm.idCursoMateria
    INNER JOIN horarios h_exist ON h_exist.idHorario = hc_exist.idHorario
    INNER JOIN horariocurso hc_new ON hc_new.idCursoMateria = p_nuevoIdCursoMateria
    INNER JOIN horarios h_new ON h_new.idHorario = hc_new.idHorario
    WHERE em.idEstudiante = p_idEstudiante
      AND em.estado = 1
      AND h_new.diaSemana = h_exist.diaSemana
      AND h_new.horaInicio < h_exist.horaFin
      AND h_new.horaFin > h_exist.horaInicio
    LIMIT 1;
END
SQL);

        DB::unprepared(<<<SQL
CREATE PROCEDURE sp_estudiante_inscribir(
    IN p_idEstudiante BIGINT UNSIGNED,
    IN p_idCursoMateria BIGINT UNSIGNED,
    IN p_idUsuario BIGINT UNSIGNED,
    IN p_direccionIp VARCHAR(45)
)
BEGIN
    DECLARE v_idInscripcion BIGINT UNSIGNED;

    INSERT INTO estudiantemateria (
        idEstudiante, idCursoMateria, fecha, estado,
        fechaA, UsuarioA, estadoA, created_at, updated_at
    ) VALUES (
        p_idEstudiante, p_idCursoMateria, NOW(), 1,
        NOW(), p_idUsuario, 1, NOW(), NOW()
    );

    SET v_idInscripcion = LAST_INSERT_ID();

    INSERT INTO auditorias (
        tabla_nombre, registro_id, accion, campo,
        valor_anterior, valor_nuevo, usuario_a,
        fecha_a, direccion_ip, created_at, updated_at
    ) VALUES (
        'estudiantemateria', v_idInscripcion, 'I', NULL,
        NULL,
        JSON_OBJECT('idEstudiante', p_idEstudiante, 'idCursoMateria', p_idCursoMateria),
        p_idUsuario, NOW(), p_direccionIp, NOW(), NOW()
    );

    SELECT v_idInscripcion AS idInscripcion;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_inscribir');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_horario_cruce');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_inscritas_ids');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_aprobadas');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_horario_texto');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_historial');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_notas');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_inscripciones');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_materias_disponibles');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_career');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_estudiante_profile');
    }
};
