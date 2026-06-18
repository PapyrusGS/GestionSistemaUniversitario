<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cursos')->insert([
            [
                'idCurso' => 1,
                'idTurno' => 1, // Mañana
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 2,
                'idTurno' => 1, // Mañana
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 3,
                'idTurno' => 1, // Mañana
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 4,
                'idTurno' => 1, // Mañana
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 5,
                'idTurno' => 1, // Mañana
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 6,
                'idTurno' => 1, // Mañana
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}