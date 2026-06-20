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
                'idCurso' => 'CUR-001',
                'capacidad' => 40,
                'estado' => true,
                
                // Campos de auditoría
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 'CUR-002',
                'capacidad' => 40,
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 'CUR-003',
                'capacidad' => 40,
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 'CUR-004',
                'capacidad' => 40,
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 'CUR-005',
                'capacidad' => 40,
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCurso' => 'CUR-006',
                'capacidad' => 40,
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