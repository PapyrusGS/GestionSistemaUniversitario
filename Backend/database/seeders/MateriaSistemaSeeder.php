<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSistemaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materias')->insert([
            // =========================================================================
            // 0. MATERIAS ELECTIVAS (IDs: 1 al 6) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 1, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: Inteligencia Artificial Aplicada',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 2, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Desarrollo de Aplicaciones Móviles',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 3, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Computación en la Nube',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 4, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Ciberseguridad Avanzada',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 5, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Gestión de Proyectos Ágiles (XP/Scrum)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 6, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Internet de las Cosas (IoT)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (IDs: 7 al 12) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 7, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Introducción a la Programación',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 8, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Cálculo I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 9, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Álgebra Lineal',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 10, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Física General',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 11, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Sistemas de Computación',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 12, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Taller de Expresión Oral y Escrita',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (IDs: 13 al 18) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 13, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 7, 'nombre' => 'Programación I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 14, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 8, 'nombre' => 'Cálculo II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 15, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 9, 'nombre' => 'Matemáticas Discretas',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 16, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 10, 'nombre' => 'Física de Semiconductores',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 17, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 11, 'nombre' => 'Arquitectura de Computadoras',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 18, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 12, 'nombre' => 'Metodología de la Investigación',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (IDs: 19 al 24) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 19, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 13, 'nombre' => 'Programación II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 20, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 14, 'nombre' => 'Ecuaciones Diferenciales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 21, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 15, 'nombre' => 'Estadística y Probabilidades',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 22, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 16, 'nombre' => 'Circuitos Electrónicos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 23, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 17, 'nombre' => 'Sistemas Operativos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 24, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 18, 'nombre' => 'Contabilidad Básica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (IDs: 25 al 30) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 25, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 19, 'nombre' => 'Estructuras de Datos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 26, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 20, 'nombre' => 'Análisis Numérico',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 27, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 21, 'nombre' => 'Investigación Operativa I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 28, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 22, 'nombre' => 'Sistemas Digitales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 29, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 23, 'nombre' => 'Sistemas Operativos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 30, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 24, 'nombre' => 'Costos y Presupuestos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (IDs: 31 al 36) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 31, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 25, 'nombre' => 'Base de Datos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 32, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 25, 'nombre' => 'Programación Web I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 33, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 27, 'nombre' => 'Investigación Operativa II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 34, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 28, 'nombre' => 'Teleinformática',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 35, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 29, 'nombre' => 'Análisis de Sistemas',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 36, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 30, 'nombre' => 'Organización y Métodos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (IDs: 37 al 42) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 37, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 31, 'nombre' => 'Base de Datos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 38, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 32, 'nombre' => 'Programación Web II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 39, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 34, 'nombre' => 'Redes de Computadoras I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 40, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 35, 'nombre' => 'Diseño de Sistemas',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 41, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 35, 'nombre' => 'Ingeniería de Software I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 42, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 36, 'nombre' => 'Sistemas Económicos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (IDs: 43 al 48) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 43, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 37, 'nombre' => 'Taller de Base de Datos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 44, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 38, 'nombre' => 'Tecnologías Emergentes',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 45, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 39, 'nombre' => 'Redes de Computadoras II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 46, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 41, 'nombre' => 'Ingeniería de Software II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 47, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 41, 'nombre' => 'Sistemas de Información I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 48, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 42, 'nombre' => 'Preparación y Evaluación de Proyectos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (IDs: 49 al 54) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 49, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 44, 'nombre' => 'Simulación de Sistemas',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 50, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 45, 'nombre' => 'Seguridad Informática',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 51, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 46, 'nombre' => 'Calidad de Software',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 52, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 47, 'nombre' => 'Sistemas de Información II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 53, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 47, 'nombre' => 'Taller de Licenciatura I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 54, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 48, 'nombre' => 'Preparación y Evaluación de Proyectos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 9º SEMESTRE (IDs: 55 al 60) - Requieren del 8º Semestre
            // =========================================================================
            [
                'idMateria' => 55, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 49, 'nombre' => 'Modelos Matemáticos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 56, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 50, 'nombre' => 'Auditoría de Sistemas',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 57, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 51, 'nombre' => 'Gestión de Proyectos de Software',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 58, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 52, 'nombre' => 'Sistemas Gerenciales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 59, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 53, 'nombre' => 'Taller de Licenciatura II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 60, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 54, 'nombre' => 'Dirección Estratégica de Empresas',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 10º SEMESTRE (IDs: 61 al 66) - Requieren del 9º Semestre
            // =========================================================================
            [
                'idMateria' => 61, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 56, 'nombre' => 'Seguridad de la Información Organizacional',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 62, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 57, 'nombre' => 'Emprendimiento de Base Tecnológica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 63, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 58, 'nombre' => 'Inteligencia de Negocios (BI)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 64, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 59, 'nombre' => 'Proyecto de Grado / Tesis',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 65, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 60, 'nombre' => 'Ética Profesional y Deontología',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 66, 'idCarrera' => 1, 'idPensum' => 1, 'idMateriaPrevia' => 55, 'nombre' => 'Modelado y Simulación Avanzada',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}