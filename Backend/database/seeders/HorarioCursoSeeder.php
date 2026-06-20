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
                'idHorarioCurso' => 1, 'idCurso' => 'CUR-001', 'idHorario' => 1, // Lunes 07:30 - 09:20
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idHorarioCurso' => 2, 'idCurso' => 'CUR-001', 'idHorario' => 2, // Lunes 09:20 - 11:10
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idHorarioCurso' => 3, 'idCurso' => 'CUR-001', 'idHorario' => 3, // Lunes 11:10 - 13:00
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 🌅 MARTES (idHorario: 7 = Bloque 1, 8 = Bloque 2)
            // =========================================================================
            [
                'idHorarioCurso' => 4, 'idCurso' => 'CUR-001', 'idHorario' => 7, // Martes 07:30 - 09:20 (Misma aula, otro día)
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idHorarioCurso' => 5, 'idCurso' => 'CUR-001', 'idHorario' => 8, // Martes 09:20 - 11:10
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 🌅 MIÉRCOLES (idHorario: 13 = Bloque 1, 14 = Bloque 2, 15 = Bloque 3)
            // Reutilizamos las mismas aulas físicas para las segundas sesiones semanales
            // =========================================================================
            [
                'idHorarioCurso' => 6, 'idCurso' => 'CUR-001', 'idHorario' => 13, // Miércoles 07:30 - 09:20
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idHorarioCurso' => 7, 'idCurso' => 'CUR-001', 'idHorario' => 14, // Miércoles 09:20 - 11:10
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idHorarioCurso' => 8, 'idCurso' => 'CUR-001', 'idHorario' => 15, // Miércoles 11:10 - 13:00
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 🌅 JUEVES (idHorario: 19 = Bloque 1, 20 = Bloque 2)
            // =========================================================================
            [
                'idHorarioCurso' => 9, 'idCurso' => 'CUR-001', 'idHorario' => 19, // Jueves 07:30 - 09:20
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idHorarioCurso' => 10, 'idCurso' => 'CUR-001', 'idHorario' => 20, // Jueves 09:20 - 11:10
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 🌅 VIERNES (idHorario: 26 = Bloque 2, 27 = Bloque 3)
            // Aquí usamos el Aula 2 (CUR-002) para variar la distribución del espacio
            // =========================================================================
            [
                'idHorarioCurso' => 11, 'idCurso' => 'CUR-002', 'idHorario' => 26, // Viernes 09:20 - 11:10
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idHorarioCurso' => 12, 'idCurso' => 'CUR-002', 'idHorario' => 27, // Viernes 11:10 - 13:00
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}