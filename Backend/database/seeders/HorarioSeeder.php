<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $horarios = [];
        
        // Días hábiles: Lunes (1) a Viernes (5)
        for ($dia = 1; $dia <= 5; $dia++) {
            
            // ==========================================
            // BLOQUES DE LA MAÑANA (07:30 a 13:00)
            // ==========================================
            // Bloque 1
            $horarios[] = [
                'diaSemana' => $dia, 'horaInicio' => '07:30:00', 'horaFin' => '09:20:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
            // Bloque 2
            $horarios[] = [
                'diaSemana' => $dia, 'horaInicio' => '09:20:00', 'horaFin' => '11:10:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
            // Bloque 3
            $horarios[] = [
                'diaSemana' => $dia, 'horaInicio' => '11:10:00', 'horaFin' => '13:00:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];

            // ==========================================
            // BLOQUES DE LA TARDE (13:30 a 18:30)
            // ==========================================
            // Bloque 4
            $horarios[] = [
                'diaSemana' => $dia, 'horaInicio' => '13:30:00', 'horaFin' => '15:10:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
            // Bloque 5
            $horarios[] = [
                'diaSemana' => $dia, 'horaInicio' => '15:10:00', 'horaFin' => '16:50:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
            // Bloque 6
            $horarios[] = [
                'diaSemana' => $dia, 'horaInicio' => '16:50:00', 'horaFin' => '18:30:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
        }

        DB::table('horarios')->insert($horarios);
    }
}