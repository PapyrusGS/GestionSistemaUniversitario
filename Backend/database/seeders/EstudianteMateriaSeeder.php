<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstudianteMateriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estudiantemateria')->insert([
            // 1. Inscripción a Introducción a la Programación (idCursoMateria: 1)
            [
                'idInscripcion' => 1,
                'idEstudiante' => 1,     // ID 1 de la tabla 'estudiante' (Williams)
                'idCursoMateria' => 1,   // Grupo de Programación
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 2. Inscripción a Cálculo I (idCursoMateria: 2)
            [
                'idInscripcion' => 2,
                'idEstudiante' => 1,
                'idCursoMateria' => 2,   // Grupo de Cálculo I
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 3. Inscripción a Álgebra Lineal (idCursoMateria: 3)
            [
                'idInscripcion' => 3,
                'idEstudiante' => 1,
                'idCursoMateria' => 3,   // Grupo de Álgebra Lineal
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 4. Inscripción a Física General (idCursoMateria: 4)
            [
                'idInscripcion' => 4,
                'idEstudiante' => 1,
                'idCursoMateria' => 4,   // Grupo de Física General
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 5. Inscripción a Sistemas de Computación (idCursoMateria: 5)
            [
                'idInscripcion' => 5,
                'idEstudiante' => 1,
                'idCursoMateria' => 5,   // Grupo de Sistemas de Computación
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // 6. Inscripción a Taller de Expresión Oral y Escrita (idCursoMateria: 6)
            [
                'idInscripcion' => 6,
                'idEstudiante' => 1,
                'idCursoMateria' => 6,   // Grupo de Taller de Expresión
                'fecha' => now(),
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