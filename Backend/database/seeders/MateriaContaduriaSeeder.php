<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaContaduriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materias')->insert([
            // =========================================================================
            // 0. MATERIAS ELECTIVAS (CON-001 al CON-006) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'CON-001', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: Sistemas Informáticos Contables (SIF)',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-002', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Contabilidad Ambiental y Social',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-003', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Planificación Tributaria Estratégica',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-004', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Peritaje Contable Judicial',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-005', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Normativa Contable Comparada (US GAAP / NIIF)',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-006', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Reorganización de Empresas y Fusiones',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (CON-007 al CON-012) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'CON-007', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Contabilidad Básica I',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-008', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Administración General',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-009', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Cálculo Aplicado a la Economía',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-010', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Introducción al Derecho e Instituciones Jurídicas',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-011', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Microeconomía General',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-012', 'idCarrera' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Técnicas de Documentación y Redacción Técnica',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (CON-013 al CON-018) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 'CON-013', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-007', 'nombre' => 'Contabilidad Básica II',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-014', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-008', 'nombre' => 'Organización y Métodos Contables',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-015', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-009', 'nombre' => 'Matemática Financiera I',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-016', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-010', 'nombre' => 'Derecho Comercial y de Sociedades Mercantiles',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-017', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-011', 'nombre' => 'Macroeconomía General',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-018', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-012', 'nombre' => 'Estadística General aplicada a la Auditoría',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (CON-019 al CON-024) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 'CON-019', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-013', 'nombre' => 'Contabilidad Intermedia I',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-020', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-013', 'nombre' => 'Contabilidad de Costos I',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-021', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-015', 'nombre' => 'Matemática Financiera II',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-022', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-016', 'nombre' => 'Derecho Laboral, Previsional y Seguridad Social',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-023', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-017', 'nombre' => 'Finanzas Públicas y Política Fiscal',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-024', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-018', 'nombre' => 'Estadística Actuarial y Muestreo Contable',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (CON-025 al CON-030) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 'CON-025', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-019', 'nombre' => 'Contabilidad Intermedia II (NIIF Plenas)',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-026', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-020', 'nombre' => 'Contabilidad de Costos II',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-027', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-019', 'nombre' => 'Contabilidad de Sociedades Comerciales',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-028', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-022', 'nombre' => 'Derecho Tributario y Código de Comercio',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-029', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-021', 'nombre' => 'Administración y Finanzas a Corto Plazo',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-030', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-019', 'nombre' => 'Sistemas Organizacionales de Control Interno',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (CON-031 al CON-036) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 'CON-031', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-025', 'nombre' => 'Contabilidad Avanzada I (Consolidación de Balances)',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-032', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-026', 'nombre' => 'Costos para la Toma de Decisiones Gerenciales',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-033', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-025', 'nombre' => 'Contabilidad Industrial y de Manufactura',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-034', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-028', 'nombre' => 'Régimen Impositivo y Liquidación de Impuestos I',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-035', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-029', 'nombre' => 'Administración Financiera de Largo Plazo',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-036', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-030', 'nombre' => 'Fundamentos y Elementos de la Auditoría',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (CON-037 al CON-042) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 'CON-037', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-031', 'nombre' => 'Contabilidad de Entidades Financieras y Seguros',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-038', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-031', 'nombre' => 'Contabilidad Gubernamental e Integrada',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-039', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-031', 'nombre' => 'Contabilidad Agropecuaria y de Extractivas',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-040', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-034', 'nombre' => 'Régimen Impositivo y Liquidación de Impuestos II',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-041', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-035', 'nombre' => 'Análisis y Formulación de Presupuestos Empresariales',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-042', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-036', 'nombre' => 'Auditoría Financiera I (Normas de Auditoría NIA)',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (CON-043 al CON-048) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 'CON-043', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-037', 'nombre' => 'Contabilidad Avanzada II (Casos Internacionales)',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-044', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-038', 'nombre' => 'Auditoría Gubernamental y Control Fiscal',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-045', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-040', 'nombre' => 'Auditoría Tributaria e Impositiva',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-046', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-041', 'nombre' => 'Preparación y Evaluación de Proyectos de Inversión',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-047', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-042', 'nombre' => 'Auditoría Financiera II (Papeles de Trabajo)',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-048', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-041', 'nombre' => 'Taller de Grado I (Metodología)',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (CON-049 al CON-054) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 'CON-049', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-047', 'nombre' => 'Auditoría Operativa y de Sistemas Contables',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-050', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-047', 'nombre' => 'Auditoría Forense (Prevención de Fraudes)',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-051', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-043', 'nombre' => 'Análisis de Mercado de Capitales e Inversión',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-052', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-046', 'nombre' => 'Gerencia Estratégica y Dirección Financiera',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-053', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-048', 'nombre' => 'Taller de Grado II (Defensa y Simulación Profesional)',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'CON-054', 'idCarrera' => 3, 'idMateriaPrevia' => 'CON-047', 'nombre' => 'Deontología Profesional y Ética del Auditor',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}