<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carreras')->insert([
            [
                'idCarrera' => 1,
                'nombre' => 'Ingeniería de Sistemas',
                'descripcion' => 'Formación en desarrollo de software, redes, seguridad informática y administración de sistemas.',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1', // ID del Administrador
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCarrera' => 2,
                'nombre' => 'Administración de Empresas',
                'descripcion' => 'Gestión estratégica, finanzas, emprendimiento y control de recursos organizacionales.',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCarrera' => 3,
                'nombre' => 'Contaduría Pública',
                'descripcion' => 'Auditoría externa, gestión de estados financieros, sistemas tributarios y costos.',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCarrera' => 4,
                'nombre' => 'Derecho',
                'descripcion' => 'Estudio de ciencias jurídicas, leyes, defensa legal y asesoría constitucional.',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idCarrera' => 5,
                'nombre' => 'Ingeniería Comercial',
                'descripcion' => 'Especialización en marketing estratégico, investigación de mercados y negociaciones internacionales.',
                'fechaRegistro' => now(),
                'estado' => true,
                'fechaA' => now(),
                'UsuarioA' => '1',
                'estadoA' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}