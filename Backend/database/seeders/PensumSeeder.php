<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PensumSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pensums')->insert([
            [
                'idPensum' => 1,
                'idCarrera' => 1, // Ingeniería de Sistemas
                'nombre' => 'Pensum de Ingeniería de Sistemas (Plan 2026)',
                'numSemestres' => 10, // 5 años de carrera
                'numMaterias' => 60,  // Exactamente 6 materias por semestre (10 x 6)
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idPensum' => 2,
                'idCarrera' => 2, // Administración de Empresas
                'nombre' => 'Pensum de Administración de Empresas (Plan G-2026)',
                'numSemestres' => 8,  // 4 años de carrera
                'numMaterias' => 48,  // Exactamente 6 materias por semestre (8 x 6)
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idPensum' => 3,
                'idCarrera' => 3, // Contaduría Pública
                'nombre' => 'Pensum de Contaduría Pública (Plan C-2026)',
                'numSemestres' => 8,  // 4 años de carrera
                'numMaterias' => 48,  // Exactamente 6 materias por semestre (8 x 6)
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idPensum' => 4,
                'idCarrera' => 4, // Derecho
                'nombre' => 'Pensum General de Derecho (Plan J-2026)',
                'numSemestres' => 10, // 5 años de carrera
                'numMaterias' => 60,  // Exactamente 6 materias por semestre (10 x 6)
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idPensum' => 5,
                'idCarrera' => 5, // Ingeniería Comercial
                'nombre' => 'Pensum de Ingeniería Comercial (Plan M-2026)',
                'numSemestres' => 8,  // 4 años de carrera
                'numMaterias' => 48,  // Exactamente 6 materias por semestre (8 x 6)
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