<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstudianteDetalleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estudiante')->insert([
            [
                'idEstudiante' => 1,
                'idUsuario' => 3,
                'nombrePadre' => 'Carlos',
                'apellidoPadre' => 'Condori',
                'numeroPadre' => '71524361',
                
                'nombreMadre' => 'Ana María',
                'apellidoMadre' => 'Vargas',
                'numeroMadre' => '60514273',
                
                
                // Campos de auditoría exigidos
                'fechaA' => now(),
                'UsuarioA' => '1', // ID del Administrador
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}