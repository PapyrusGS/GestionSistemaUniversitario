<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstudianteCarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estudiante_carrera')->insert([
            [
                'idEstudianteCarrera' => 1,
                'idEstudiante' => 1, // ID de nuestro usuario estudiante (Williams)
                'idCarrera' => 1,    // Ingeniería de Sistemas
                'fechaRegistro' => now(),
                'estado' => true,
                
                // Campos de auditoría exigidos
                'fechaA' => now(),
                'UsuarioA' => '1',   // ID del Administrador
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}