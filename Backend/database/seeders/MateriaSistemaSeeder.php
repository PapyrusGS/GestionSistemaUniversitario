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
            // 0. MATERIAS ELECTIVAS (SIS-001 al SIS-006) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'SIS-001', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: Inteligencia Artificial Aplicada',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-002', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Desarrollo de Aplicaciones Móviles',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-003', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Computación en la Nube',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-004', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Ciberseguridad Avanzada',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-005', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Gestión de Proyectos Ágiles (XP/Scrum)',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-006', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Internet de las Cosas (IoT)',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (SIS-007 al SIS-012) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'SIS-007', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Introducción a la Programación',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-008', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Cálculo I',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-009', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Álgebra Lineal',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-010', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Física General',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-011', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Sistemas de Computación',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-012', 'idCarrera' => 1, 'idMateriaPrevia' => null, 'nombre' => 'Taller de Expresión Oral y Escrita',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (SIS-013 al SIS-018) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-013', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-007', 'nombre' => 'Programación I',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-014', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-008', 'nombre' => 'Cálculo II',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-015', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-009', 'nombre' => 'Matemáticas Discretas',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-016', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-010', 'nombre' => 'Física de Semiconductores',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-017', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-011', 'nombre' => 'Arquitectura de Computadoras',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-018', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-012', 'nombre' => 'Metodología de la Investigación',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (SIS-019 al SIS-024) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-019', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-013', 'nombre' => 'Programación II',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-020', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-014', 'nombre' => 'Ecuaciones Diferenciales',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-021', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-015', 'nombre' => 'Estadística y Probabilidades',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-022', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-016', 'nombre' => 'Circuitos Electrónicos',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-023', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-017', 'nombre' => 'Sistemas Operativos I',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-024', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-018', 'nombre' => 'Contabilidad Básica',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (SIS-025 al SIS-030) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-025', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-019', 'nombre' => 'Estructuras de Datos',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-026', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-020', 'nombre' => 'Análisis Numérico',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-027', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-021', 'nombre' => 'Investigación Operativa I',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-028', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-022', 'nombre' => 'Sistemas Digitales',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-029', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-023', 'nombre' => 'Sistemas Operativos II',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-030', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-024', 'nombre' => 'Costos y Presupuestos',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (SIS-031 al SIS-036) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-031', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-025', 'nombre' => 'Base de Datos I',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-032', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-025', 'nombre' => 'Programación Web I',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-033', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-027', 'nombre' => 'Investigación Operativa II',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-034', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-028', 'nombre' => 'Teleinformática',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-035', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-029', 'nombre' => 'Análisis de Sistemas',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-036', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-030', 'nombre' => 'Organización y Métodos',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (SIS-037 al SIS-042) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-037', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-031', 'nombre' => 'Base de Datos II',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-038', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-032', 'nombre' => 'Programación Web II',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-039', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-034', 'nombre' => 'Redes de Computadoras I',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-040', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-035', 'nombre' => 'Diseño de Sistemas',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-041', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-035', 'nombre' => 'Ingeniería de Software I',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-042', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-036', 'nombre' => 'Sistemas Económicos',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (SIS-043 al SIS-048) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-043', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-037', 'nombre' => 'Taller de Base de Datos',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-044', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-038', 'nombre' => 'Tecnologías Emergentes',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-045', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-039', 'nombre' => 'Redes de Computadoras II',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-046', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-041', 'nombre' => 'Ingeniería de Software II',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-047', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-041', 'nombre' => 'Sistemas de Información I',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-048', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-042', 'nombre' => 'Preparación y Evaluación de Proyectos I',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (SIS-049 al SIS-054) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-049', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-044', 'nombre' => 'Simulación de Sistemas',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-050', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-045', 'nombre' => 'Seguridad Informática',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-051', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-046', 'nombre' => 'Calidad de Software',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-052', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-047', 'nombre' => 'Sistemas de Información II',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-053', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-047', 'nombre' => 'Taller de Licenciatura I',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-054', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-048', 'nombre' => 'Preparación y Evaluación de Proyectos II',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 9º SEMESTRE (SIS-055 al SIS-060) - Requieren del 8º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-055', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-049', 'nombre' => 'Modelos Matemáticos',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-056', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-050', 'nombre' => 'Auditoría de Sistemas',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-057', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-051', 'nombre' => 'Gestión de Proyectos de Software',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-058', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-052', 'nombre' => 'Sistemas Gerenciales',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-059', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-053', 'nombre' => 'Taller de Licenciatura II',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-060', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-054', 'nombre' => 'Dirección Estratégica de Empresas',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 10º SEMESTRE (SIS-061 al SIS-066) - Requieren del 9º Semestre
            // =========================================================================
            [
                'idMateria' => 'SIS-061', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-056', 'nombre' => 'Seguridad de la Información Organizacional',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-062', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-057', 'nombre' => 'Emprendimiento de Base Tecnológica',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-063', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-058', 'nombre' => 'Inteligencia de Negocios (BI)',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-064', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-059', 'nombre' => 'Proyecto de Grado / Tesis',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-065', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-060', 'nombre' => 'Ética Profesional y Deontología',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'SIS-066', 'idCarrera' => 1, 'idMateriaPrevia' => 'SIS-055', 'nombre' => 'Modelado y Simulación Avanzada',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}