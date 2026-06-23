<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoMateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos_materias')->insert([
            // 1. Introducción a la Programación (idMateria: 'SIS-007') -> Curso 'CUR-001'
            [
                'idCursoMateria' => 1,
                'idCurso' => 'CUR-101',   
                'idMateria' => 'SIS-007', 
                'idDocente' => 2,         // Nuestro único docente registrado
                'idPeriodo' => 1,         // I-2026
                'fechaInicio' => '2026-02-02 00:00:00',
                'fechaFin' => '2026-06-26 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 2. Cálculo I (idMateria: 'SIS-008') -> Curso 'CUR-002'
            [
                'idCursoMateria' => 2,
                'idCurso' => 'CUR-101',   
                'idMateria' => 'SIS-008', 
                'idDocente' => 2,
                'idPeriodo' => 1,
                'fechaInicio' => '2026-02-02 00:00:00',
                'fechaFin' => '2026-06-26 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 3. Álgebra Lineal (idMateria: 'SIS-009') -> Curso 'CUR-003'
            [
                'idCursoMateria' => 3,
                'idCurso' => 'CUR-101',   
                'idMateria' => 'SIS-009', 
                'idDocente' => 2,
                'idPeriodo' => 1,
                'fechaInicio' => '2026-02-02 00:00:00',
                'fechaFin' => '2026-06-26 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 4. Física General (idMateria: 'SIS-010') -> Curso 'CUR-004'
            [
                'idCursoMateria' => 4,
                'idCurso' => 'CUR-101',   
                'idMateria' => 'SIS-010', 
                'idDocente' => 2,
                'idPeriodo' => 1,
                'fechaInicio' => '2026-02-02 00:00:00',
                'fechaFin' => '2026-06-26 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 5. Sistemas de Computación (idMateria: 'SIS-011') -> Curso 'CUR-005'
            [
                'idCursoMateria' => 5,
                'idCurso' => 'CUR-101',   
                'idMateria' => 'SIS-011', 
                'idDocente' => 2,
                'idPeriodo' => 1,
                'fechaInicio' => '2026-02-02 00:00:00',
                'fechaFin' => '2026-06-26 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 6. Taller de Expresión Oral y Escrita (idMateria: 'SIS-012') -> Curso 'CUR-006'
            [
                'idCursoMateria' => 6,
                'idCurso' => 'CUR-101',   
                'idMateria' => 'SIS-012', 
                'idDocente' => 2,
                'idPeriodo' => 1,
                'fechaInicio' => '2026-02-02 00:00:00',
                'fechaFin' => '2026-06-26 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // ========================================================================
            // 📚 SEGUNDO SEMESTRE – II-2026 (Periodo activo)
            // ========================================================================
            // 7. Programación I (idMateria: 'SIS-013', prerreq: SIS-007)
            [
                'idCursoMateria' => 7,
                'idCurso' => 'CUR-102',
                'idMateria' => 'SIS-013',
                'idDocente' => 2,
                'idPeriodo' => 2,
                'fechaInicio' => '2026-07-27 00:00:00',
                'fechaFin' => '2026-12-04 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 8. Cálculo II (idMateria: 'SIS-014', prerreq: SIS-008)
            [
                'idCursoMateria' => 8,
                'idCurso' => 'CUR-103',
                'idMateria' => 'SIS-014',
                'idDocente' => 2,
                'idPeriodo' => 2,
                'fechaInicio' => '2026-07-27 00:00:00',
                'fechaFin' => '2026-12-04 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 9. Matemáticas Discretas (idMateria: 'SIS-015', prerreq: SIS-009)
            [
                'idCursoMateria' => 9,
                'idCurso' => 'CUR-104',
                'idMateria' => 'SIS-015',
                'idDocente' => 2,
                'idPeriodo' => 2,
                'fechaInicio' => '2026-07-27 00:00:00',
                'fechaFin' => '2026-12-04 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 10. Física de Semiconductores (idMateria: 'SIS-016', prerreq: SIS-010)
            [
                'idCursoMateria' => 10,
                'idCurso' => 'CUR-105',
                'idMateria' => 'SIS-016',
                'idDocente' => 2,
                'idPeriodo' => 2,
                'fechaInicio' => '2026-07-27 00:00:00',
                'fechaFin' => '2026-12-04 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 11. Arquitectura de Computadoras (idMateria: 'SIS-017', prerreq: SIS-011)
            [
                'idCursoMateria' => 11,
                'idCurso' => 'CUR-106',
                'idMateria' => 'SIS-017',
                'idDocente' => 2,
                'idPeriodo' => 2,
                'fechaInicio' => '2026-07-27 00:00:00',
                'fechaFin' => '2026-12-04 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 12. Metodología de la Investigación (idMateria: 'SIS-018', prerreq: SIS-012)
            [
                'idCursoMateria' => 12,
                'idCurso' => 'CUR-107',
                'idMateria' => 'SIS-018',
                'idDocente' => 2,
                'idPeriodo' => 2,
                'fechaInicio' => '2026-07-27 00:00:00',
                'fechaFin' => '2026-12-04 23:59:59',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}