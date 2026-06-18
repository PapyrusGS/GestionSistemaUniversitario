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
        ]);
    }
}
