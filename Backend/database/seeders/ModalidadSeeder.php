<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalidadSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('modalidades')->insert([
            [
                'idModalidad' => 1,
                'nombre' => 'Semestral',
                'duracionSemanas' => 20, // Aproximadamente 5 meses de clases
                'maxMaterias' => 6,      // Máximo de materias por semestre
                'fechaRegistro' => now(),
                'estado' => true,
                // Campos de auditoría solicitados
                'fechaA' => now(),
                'UsuarioA' => '1', // ID del Administrador
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idModalidad' => 2,
                'nombre' => 'Modular',
                'duracionSemanas' => 4,  // 1 mes por módulo/materia
                'maxMaterias' => 1,      // 1 materia fuerte a la vez
                'fechaRegistro' => now(),
                'estado' => true,
                // Campos de auditoría solicitados
                'fechaA' => now(),
                'UsuarioA' => '1', // ID del Administrador
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}