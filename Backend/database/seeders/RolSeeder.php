<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'idRol' => 1,
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema de gestión académica y administrativa.',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idRol' => 2,
                'nombre' => 'Docente',
                'descripcion' => 'Gestión de cursos, control de asistencia y registro de calificaciones.',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idRol' => 3,
                'nombre' => 'Estudiante',
                'descripcion' => 'Consulta de notas, historial académico e inscripciones.',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}