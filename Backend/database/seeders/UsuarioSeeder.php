<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            // 1. Usuario Administrador
            [
                'idRol' => 1, // Administrador
                'nombre1' => 'Admin',
                'nombre2' => null,
                'apellido1' => 'Primer',
                'apellido2' => 'Usuario',
                'ci' => '1234567',
                'correo' => 'admin@sistema.com',
                'password' => Hash::make('123456'), // Contraseña encriptada
                'fechaRegistro' => now(),
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Usuario Docente
            [
                'idRol' => 2, // Docente
                'nombre1' => 'Docente',
                'nombre2' => null,
                'apellido1' => 'Primer',
                'apellido2' => 'Usuario',
                'ci' => '7654321',
                'correo' => 'docente@sistema.com',
                'password' => Hash::make('123456'),
                'fechaRegistro' => now(),
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Usuario Estudiante
            [
                'idRol' => 3, // Estudiante
                'nombre1' => 'Estudiante',
                'nombre2' => null,
                'apellido1' => 'Primer',
                'apellido2' => 'Usuario',
                'ci' => '9876543',
                'correo' => 'estudiante@sistema.com',
                'password' => Hash::make('123456'),
                'fechaRegistro' => now(),
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}