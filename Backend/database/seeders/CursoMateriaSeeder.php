<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoMateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos_materias')->insert([
            // ========================================================================
            // 📚 INGENIERÍA DE SISTEMAS – I-2026
            // ========================================================================
            ['idCursoMateria' => 1,  'idCurso' => 'CUR-101', 'idMateria' => 'SIS-007', 'idDocente' => 2, 'idPeriodo' => 1, 'fechaInicio' => '2026-02-02 00:00:00', 'fechaFin' => '2026-06-26 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 2,  'idCurso' => 'CUR-101', 'idMateria' => 'SIS-008', 'idDocente' => 2, 'idPeriodo' => 1, 'fechaInicio' => '2026-02-02 00:00:00', 'fechaFin' => '2026-06-26 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 3,  'idCurso' => 'CUR-101', 'idMateria' => 'SIS-009', 'idDocente' => 2, 'idPeriodo' => 1, 'fechaInicio' => '2026-02-02 00:00:00', 'fechaFin' => '2026-06-26 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 4,  'idCurso' => 'CUR-101', 'idMateria' => 'SIS-010', 'idDocente' => 2, 'idPeriodo' => 1, 'fechaInicio' => '2026-02-02 00:00:00', 'fechaFin' => '2026-06-26 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 5,  'idCurso' => 'CUR-101', 'idMateria' => 'SIS-011', 'idDocente' => 2, 'idPeriodo' => 1, 'fechaInicio' => '2026-02-02 00:00:00', 'fechaFin' => '2026-06-26 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 6,  'idCurso' => 'CUR-101', 'idMateria' => 'SIS-012', 'idDocente' => 2, 'idPeriodo' => 1, 'fechaInicio' => '2026-02-02 00:00:00', 'fechaFin' => '2026-06-26 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],

            // ========================================================================
            // 📚 INGENIERÍA DE SISTEMAS – II-2026
            // ========================================================================
            ['idCursoMateria' => 7,  'idCurso' => 'CUR-102', 'idMateria' => 'SIS-013', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 8,  'idCurso' => 'CUR-103', 'idMateria' => 'SIS-014', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 9,  'idCurso' => 'CUR-104', 'idMateria' => 'SIS-015', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 10, 'idCurso' => 'CUR-105', 'idMateria' => 'SIS-016', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 11, 'idCurso' => 'CUR-106', 'idMateria' => 'SIS-017', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 12, 'idCurso' => 'CUR-107', 'idMateria' => 'SIS-018', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],

            // ========================================================================
            // 📚 ADMINISTRACIÓN DE EMPRESAS – II-2026 (Semestre 1)
            // ========================================================================
            ['idCursoMateria' => 13, 'idCurso' => 'CUR-108', 'idMateria' => 'ADM-007', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 14, 'idCurso' => 'CUR-109', 'idMateria' => 'ADM-008', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 15, 'idCurso' => 'CUR-110', 'idMateria' => 'ADM-009', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 16, 'idCurso' => 'CUR-201', 'idMateria' => 'ADM-010', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 17, 'idCurso' => 'CUR-202', 'idMateria' => 'ADM-011', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 18, 'idCurso' => 'CUR-203', 'idMateria' => 'ADM-012', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],

            // ========================================================================
            // 📚 CONTADURÍA PÚBLICA – II-2026 (Semestre 1)
            // ========================================================================
            ['idCursoMateria' => 19, 'idCurso' => 'CUR-204', 'idMateria' => 'CON-007', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 20, 'idCurso' => 'CUR-205', 'idMateria' => 'CON-008', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 21, 'idCurso' => 'CUR-206', 'idMateria' => 'CON-009', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 22, 'idCurso' => 'CUR-207', 'idMateria' => 'CON-010', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 23, 'idCurso' => 'CUR-208', 'idMateria' => 'CON-011', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 24, 'idCurso' => 'CUR-209', 'idMateria' => 'CON-012', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],

            // ========================================================================
            // 📚 DERECHO – II-2026 (Semestre 1)
            // ========================================================================
            ['idCursoMateria' => 25, 'idCurso' => 'CUR-210', 'idMateria' => 'DER-007', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 26, 'idCurso' => 'CUR-301', 'idMateria' => 'DER-008', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 27, 'idCurso' => 'CUR-302', 'idMateria' => 'DER-009', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 28, 'idCurso' => 'CUR-303', 'idMateria' => 'DER-010', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 29, 'idCurso' => 'CUR-304', 'idMateria' => 'DER-011', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 30, 'idCurso' => 'CUR-305', 'idMateria' => 'DER-012', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],

            // ========================================================================
            // 📚 INGENIERÍA COMERCIAL – II-2026 (Semestre 1)
            // ========================================================================
            ['idCursoMateria' => 31, 'idCurso' => 'CUR-306', 'idMateria' => 'COM-007', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 32, 'idCurso' => 'CUR-307', 'idMateria' => 'COM-008', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 33, 'idCurso' => 'CUR-308', 'idMateria' => 'COM-009', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 34, 'idCurso' => 'CUR-309', 'idMateria' => 'COM-010', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 35, 'idCurso' => 'CUR-310', 'idMateria' => 'COM-011', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
            ['idCursoMateria' => 36, 'idCurso' => 'CUR-401', 'idMateria' => 'COM-012', 'idDocente' => 2, 'idPeriodo' => 2, 'fechaInicio' => '2026-07-27 00:00:00', 'fechaFin' => '2026-12-04 23:59:59', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
