<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\MateriaIngenieriaComercialSeeder;


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
            EstudianteDetalleSeeder::class,
            CarreraSeeder::class,     // Depende de que el admin ya exista para la auditoría
            MateriaSistemaSeeder::class,
            MateriaAdministracionSeeder::class,
            MateriaContaduriaSeeder::class,
            MateriaDerechoSeeder::class,
            MateriaIngenieriaComercialSeeder::class,
            PeriodoSeeder::class,       // Crea los periodos (idPeriodo: 1)
            HorarioSeeder::class,       // Crea los horarios base
            CursoSeeder::class,         // Crea las aulas string (CUR-001...)
              // Mapea la cuadrícula de ocupación de aulas
            EstudianteCarreraSeeder::class,
            CursoMateriaSeeder::class,
            HorarioCursoSeeder::class,
            EstudianteMateriaSeeder::class
        ]);
    }
}
