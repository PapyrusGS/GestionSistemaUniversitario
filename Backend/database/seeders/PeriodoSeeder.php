<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('periodos')->insert([
            [
                'idPeriodo' => 1,
                'nombre' => 'I-2026',
                'fechaInicioSemestre' => '2026-02-02', // Inicio habitual de clases en febrero
                'fechaFinSemestre' => '2026-06-26',   // Fin a finales de junio
                'estado' => false,                     // Ya concluyó o está cerrando
            ],
            [
                'idPeriodo' => 2,
                'nombre' => 'II-2026',
                'fechaInicioSemestre' => '2026-07-27', // Inicio de segundo semestre tras el receso de invierno
                'fechaFinSemestre' => '2026-12-04',   // Fin a inicios de diciembre
                'estado' => true,                      // Periodo actual activo
            ],
            [
                'idPeriodo' => 3,
                'nombre' => 'I-2027',
                'fechaInicioSemestre' => '2027-02-01',
                'fechaFinSemestre' => '2027-06-25',
                'estado' => false,                     // Planificado / Inactivo
            ],
            [
                'idPeriodo' => 4,
                'nombre' => 'II-2027',
                'fechaInicioSemestre' => '2027-07-26',
                'fechaFinSemestre' => '2027-12-03',
                'estado' => false,                     // Planificado / Inactivo
            ],
        ]);
    }
}