<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoDetalleSeeder extends Seeder
{
    public function run(): void
    {
        $detalles = [];
        
        // Días hábiles de Lunes (1) a Viernes (5)
        for ($dia = 1; $dia <= 5; $dia++) {
            
            // =========================================================================
            // TURNO MAÑANA (idTurno: 1) -> 3 bloques de 1h 50m (Límite estricto 07:30 - 13:00)
            // =========================================================================
            $detalles[] = [
                'idTurno' => 1, 'diaSemana' => $dia, 'horaInicio' => '07:30:00', 'horaFin' => '09:20:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
            $detalles[] = [
                'idTurno' => 1, 'diaSemana' => $dia, 'horaInicio' => '09:20:00', 'horaFin' => '11:10:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
            $detalles[] = [
                'idTurno' => 1, 'diaSemana' => $dia, 'horaInicio' => '11:10:00', 'horaFin' => '13:00:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];

            // =========================================================================
            // TURNO TARDE (idTurno: 2) -> 3 bloques de 1h 40m (Límite estricto 13:30 - 18:30)
            // =========================================================================
            $detalles[] = [
                'idTurno' => 2, 'diaSemana' => $dia, 'horaInicio' => '13:30:00', 'horaFin' => '15:10:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
            $detalles[] = [
                'idTurno' => 2, 'diaSemana' => $dia, 'horaInicio' => '15:10:00', 'horaFin' => '16:50:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
            $detalles[] = [
                'idTurno' => 2, 'diaSemana' => $dia, 'horaInicio' => '16:50:00', 'horaFin' => '18:30:00',
                'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ];
        }

        DB::table('turno_detalles')->insert($detalles);
    }
}