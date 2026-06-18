<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inscripciones')->insert([
            [
                'idInscripcion' => 1,
                'idEstudiante' => 3,     // ID de Williams
                'idCursoMateria' => 1,   // Introducción a la Programación
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idInscripcion' => 2,
                'idEstudiante' => 3,
                'idCursoMateria' => 2,   // Cálculo I
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idInscripcion' => 3,
                'idEstudiante' => 3,
                'idCursoMateria' => 3,   // Álgebra Lineal
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idInscripcion' => 4,
                'idEstudiante' => 3,
                'idCursoMateria' => 4,   // Física General
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idInscripcion' => 5,
                'idEstudiante' => 3,
                'idCursoMateria' => 5,   // Sistemas de Computación
                'fecha' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idInscripcion' => 6,
                'idEstudiante' => 3,
                'idCursoMateria' => 6,   // Taller de Expresión Oral y Escrita
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