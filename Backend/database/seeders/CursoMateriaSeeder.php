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
        ]);
    }
}