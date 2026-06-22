<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        $cursos = [];

        // =========================================================================
        // 🏫 GENERACIÓN DE 50 CURSOS (10 por cada uno de los 5 pisos)
        // CUR-101 hasta CUR-110, CUR-201 hasta CUR-210, etc.
        // =========================================================================
        for ($piso = 1; $piso <= 5; $piso++) {
            for ($aula = 1; $aula <= 10; $aula++) {
                // str_pad añade un cero a la izquierda si el número de aula es menor a 10 (ej: 01, 02... 10)
                $idCurso = 'CUR-' . $piso . str_pad($aula, 2, '0', STR_PAD_LEFT);

                $cursos[] = [
                    'idCurso' => $idCurso,
                    'capacidad' => 40,
                    'estado' => true,
                    'fechaA' => now(),
                    'UsuarioA' => '1',
                    'estadoA' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // =========================================================================
        // 🧪 GENERACIÓN DE 10 LABORATORIOS (2 por cada uno de los 5 pisos)
        // LAB-101 y LAB-102 hasta LAB-501 y LAB-502
        // =========================================================================
        for ($piso = 1; $piso <= 5; $piso++) {
            for ($lab = 1; $lab <= 2; $lab++) {
                $idLab = 'LAB-' . $piso . str_pad($lab, 2, '0', STR_PAD_LEFT);

                $cursos[] = [
                    'idCurso' => $idLab,
                    'capacidad' => 40, // Puedes cambiar la capacidad si los laboratorios son más grandes o pequeños
                    'estado' => true,
                    'fechaA' => now(),
                    'UsuarioA' => '1',
                    'estadoA' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Inserción masiva de los 60 registros en un solo viaje a la base de datos
        DB::table('cursos')->insert($cursos);
    }
}