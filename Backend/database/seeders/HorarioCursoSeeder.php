<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioCursoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('horariocurso')->insert([
            // =========================================================================
            // 🌅 LUNES (idHorario: 1 = Bloque 1, 2 = Bloque 2, 3 = Bloque 3)
            // =========================================================================
            [
                'idHorarioCurso' => 1, 'idCursoMateria' => '1', 'idHorario' => 1, // Lunes 07:30 - 09:20
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            
            // =========================================================================
            // 🌅 MARTES (idHorario: 7 = Bloque 1, 8 = Bloque 2)
            // =========================================================================
            [
                'idHorarioCurso' => 2, 'idCursoMateria' => '2', 'idHorario' => 7, // Martes 07:30 - 09:20 (Misma aula, otro día)
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            

            // =========================================================================
            // 🌅 MIÉRCOLES (idHorario: 13 = Bloque 1, 14 = Bloque 2, 15 = Bloque 3)
            // Reutilizamos las mismas aulas físicas para las segundas sesiones semanales
            // =========================================================================
            [
                'idHorarioCurso' => 3, 'idCursoMateria' => '3', 'idHorario' => 13, // Miércoles 07:30 - 09:20
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            

            // =========================================================================
            // 🌅 JUEVES (idHorario: 19 = Bloque 1, 20 = Bloque 2)
            // =========================================================================
            [
                'idHorarioCurso' => 4, 'idCursoMateria' => '4', 'idHorario' => 19, // Jueves 07:30 - 09:20
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
           

            // =========================================================================
            // 🌅 VIERNES (idHorario: 26 = Bloque 2, 27 = Bloque 3)
            // Aquí usamos el Aula 2 (CUR-002) para variar la distribución del espacio
            // =========================================================================
            [
                'idHorarioCurso' => 5, 'idCursoMateria' => '5', 'idHorario' => 25, // Viernes 09:20 - 11:10
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
          
            
        ]);
    }
}