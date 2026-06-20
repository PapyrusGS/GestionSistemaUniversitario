<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaAdministracionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materias')->insert([
            // =========================================================================
            // 0. MATERIAS ELECTIVAS (IDs: ADM-001 al ADM-006) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'ADM-001', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: E-Commerce y Negocios Digitales',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-002', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Coaching y Liderazgo Organizacional',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-003', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Neuromarketing',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-004', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Responsabilidad Social Empresarial',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-005', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Gestión de la Innovación',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-006', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Franquicias y Modelos de Expansión',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (IDs: ADM-007 al ADM-012) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'ADM-007', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Administración I (Principios de Administración)',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-008', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Contabilidad General I',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-009', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Matemática Financiera I',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-010', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Introducción al Derecho Económico',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-011', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Microeconomía I',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-012', 'idCarrera' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Técnicas de Estudio e Investigación',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (IDs: ADM-013 al ADM-018) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 'ADM-013', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-007', 'nombre' => 'Administración II (Proceso Administrativo)',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-014', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-008', 'nombre' => 'Contabilidad General II',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-015', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-009', 'nombre' => 'Matemática Financiera II',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-016', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-010', 'nombre' => 'Derecho Comercial y Societario',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-017', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-011', 'nombre' => 'Microeconomía II',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-018', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-012', 'nombre' => 'Estadística Descriptiva',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (IDs: ADM-019 al ADM-024) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 'ADM-019', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-013', 'nombre' => 'Comportamiento Organizacional',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-020', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-014', 'nombre' => 'Contabilidad de Costos I',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-021', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-015', 'nombre' => 'Finanzas de Empresas I',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-022', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-016', 'nombre' => 'Derecho Laboral y Seguridad Social',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-023', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-017', 'nombre' => 'Macroeconomía I',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-024', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-018', 'nombre' => 'Estadística Inferencial',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (IDs: ADM-025 al ADM-030) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 'ADM-025', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-019', 'nombre' => 'Administración de Recursos Humanos I',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-026', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-020', 'nombre' => 'Contabilidad de Costos II',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-027', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-021', 'nombre' => 'Finanzas de Empresas II',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-028', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-019', 'nombre' => 'Mercadotecnia I',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-029', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-023', 'nombre' => 'Macroeconomía II',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-030', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-024', 'nombre' => 'Investigación de Operaciones',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (IDs: ADM-031 al ADM-036) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 'ADM-031', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-025', 'nombre' => 'Administración de Recursos Humanos II',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-032', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-027', 'nombre' => 'Análisis e Interpretación de Estados Financieros',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-033', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-028', 'nombre' => 'Mercadotecnia II',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-034', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-025', 'nombre' => 'Administración de la Producción I',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-035', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-028', 'nombre' => 'Investigación de Mercados',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-036', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-029', 'nombre' => 'Comercio Internacional',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (IDs: ADM-037 al ADM-042) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 'ADM-037', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-031', 'nombre' => 'Diseño y Estructuras Organizacionales',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-038', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-032', 'nombre' => 'Presupuestos y Control de Gestión',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-039', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-033', 'nombre' => 'Estrategias de Precios y Distribución',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-040', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-034', 'nombre' => 'Administración de la Producción II',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-041', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-031', 'nombre' => 'Sistemas de Información Gerencial',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-042', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-036', 'nombre' => 'Preparación y Evaluación de Proyectos I',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (IDs: ADM-043 al ADM-048) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 'ADM-043', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-037', 'nombre' => 'Dirección Estratégica I',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-044', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-038', 'nombre' => 'Ingeniería Financiera',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-045', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-039', 'nombre' => 'Gerencia de Marca y Producto',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-046', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-040', 'nombre' => 'Logística y Cadena de Suministros',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-047', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-041', 'nombre' => 'Taller de Grado I',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-048', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-042', 'nombre' => 'Preparación y Evaluación de Proyectos II',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (IDs: ADM-049 al ADM-054) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 'ADM-049', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-043', 'nombre' => 'Dirección Estratégica II (Simulación Empresarial)',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-050', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-044', 'nombre' => 'Mercado de Valores y Capitales',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-051', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-045', 'nombre' => 'Auditoría Administrativa',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-052', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-043', 'nombre' => 'Creación de Empresas (Emprendimiento)',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-053', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-047', 'nombre' => 'Taller de Grado II (Defensa de Proyecto)',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'ADM-054', 'idCarrera' => 2, 'idMateriaPrevia' => 'ADM-043', 'nombre' => 'Ética de los Negocios y Deontología Empresarial',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}