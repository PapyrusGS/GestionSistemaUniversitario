<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // El orden aquí importa mucho por la llave foránea
        $this->call([
            RolSeeder::class,
            UsuarioSeeder::class,
            CarreraSeeder::class,     // Depende de que el admin ya exista para la auditoría
            MateriaSistemaSeeder::class,
            MateriaAdministracionSeeder::class,
            MateriaContaduriaSeeder::class,
            MateriaDerechoSeeder::class,
            MateriaIngenieriaComercialSeeder::class,
            EstudianteCarreraSeeder::class,
            TurnoDetalleSeeder::class,
            CursoSeeder::class,
            CursoMateriaSeeder::class,
            InscripcionSeeder::class
        ]);
    }
}
