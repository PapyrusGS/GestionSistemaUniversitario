<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoMateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos_materias')->insert([
            // 1. Introducción a la Programación (idMateria: 7) -> Curso 1
            [
                'idCursoMateria' => 1,
                'idCurso' => 1,
                'idMateria' => 7,
                'idDocente' => 2, // Nuestro único docente registrado
                'idTurno' => 1,   // Mañana
                'fechaInicio' => '2026-02-01 00:00:00',
                'fechaFin' => '2026-06-30 23:59:59',
                'maxInscritos' => 40,
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 2. Cálculo I (idMateria: 8) -> Curso 2
            [
                'idCursoMateria' => 2,
                'idCurso' => 2,
                'idMateria' => 8,
                'idDocente' => 2,
                'idTurno' => 1,
                'fechaInicio' => '2026-02-01 00:00:00',
                'fechaFin' => '2026-06-30 23:59:59',
                'maxInscritos' => 40,
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 3. Álgebra Lineal (idMateria: 9) -> Curso 3
            [
                'idCursoMateria' => 3,
                'idCurso' => 3,
                'idMateria' => 9,
                'idDocente' => 2,
                'idTurno' => 1,
                'fechaInicio' => '2026-02-01 00:00:00',
                'fechaFin' => '2026-06-30 23:59:59',
                'maxInscritos' => 40,
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 4. Física General (idMateria: 10) -> Curso 4
            [
                'idCursoMateria' => 4,
                'idCurso' => 4,
                'idMateria' => 10,
                'idDocente' => 2,
                'idTurno' => 1,
                'fechaInicio' => '2026-02-01 00:00:00',
                'fechaFin' => '2026-06-30 23:59:59',
                'maxInscritos' => 40,
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 5. Sistemas de Computación (idMateria: 11) -> Curso 5
            [
                'idCursoMateria' => 5,
                'idCurso' => 5,
                'idMateria' => 11,
                'idDocente' => 2,
                'idTurno' => 1,
                'fechaInicio' => '2026-02-01 00:00:00',
                'fechaFin' => '2026-06-30 23:59:59',
                'maxInscritos' => 40,
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 6. Taller de Expresión Oral y Escrita (idMateria: 12) -> Curso 6
            [
                'idCursoMateria' => 6,
                'idCurso' => 6,
                'idMateria' => 12,
                'idDocente' => 2,
                'idTurno' => 1,
                'fechaInicio' => '2026-02-01 00:00:00',
                'fechaFin' => '2026-06-30 23:59:59',
                'maxInscritos' => 40,
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