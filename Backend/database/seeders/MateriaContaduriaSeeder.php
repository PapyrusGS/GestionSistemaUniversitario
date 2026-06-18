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
            // 0. MATERIAS ELECTIVAS (IDs: 121 al 126) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 121, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: Sistemas Informáticos Contables (SIF)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 122, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Contabilidad Ambiental y Social',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 123, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Planificación Tributaria Estratégica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 124, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Peritaje Contable Judicial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 125, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Normativa Contable Comparada (US GAAP / NIIF)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 126, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Reorganización de Empresas y Fusiones',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (IDs: 127 al 132) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 127, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Contabilidad Básica I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 132, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Administración General',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 128, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Cálculo Aplicado a la Economía',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 129, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Introducción al Derecho e Instituciones Jurídicas',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 130, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Microeconomía General',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 131, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => null, 'nombre' => 'Técnicas de Documentación y Redacción Técnica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (IDs: 133 al 138) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 133, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 127, 'nombre' => 'Contabilidad Básica II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 134, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 132, 'nombre' => 'Organización y Métodos Contables',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 135, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 128, 'nombre' => 'Matemática Financiera I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 136, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 129, 'nombre' => 'Derecho Comercial y de Sociedades Mercantiles',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 137, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 130, 'nombre' => 'Macroeconomía General',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 138, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 131, 'nombre' => 'Estadística General aplicada a la Auditoría',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (IDs: 139 al 144) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 139, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 133, 'nombre' => 'Contabilidad Intermedia I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 140, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 133, 'nombre' => 'Contabilidad de Costos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 141, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 135, 'nombre' => 'Matemática Financiera II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 142, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 136, 'nombre' => 'Derecho Laboral, Previsional y Seguridad Social',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 143, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 137, 'nombre' => 'Finanzas Públicas y Política Fiscal',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 144, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 138, 'nombre' => 'Estadística Actuarial y Muestreo Contable',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (IDs: 145 al 150) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 145, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 139, 'nombre' => 'Contabilidad Intermedia II (NIIF Plenas)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 146, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 140, 'nombre' => 'Contabilidad de Costos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 147, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 139, 'nombre' => 'Contabilidad de Sociedades Comerciales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 148, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 142, 'nombre' => 'Derecho Tributario y Código de Comercio',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 149, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 141, 'nombre' => 'Administración y Finanzas a Corto Plazo',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 150, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 139, 'nombre' => 'Sistemas Organizacionales de Control Interno',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (IDs: 151 al 156) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 151, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 145, 'nombre' => 'Contabilidad Avanzada I (Consolidación de Balances)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 152, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 146, 'nombre' => 'Costos para la Toma de Decisiones Gerenciales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 153, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 145, 'nombre' => 'Contabilidad Industrial y de Manufactura',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 154, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 148, 'nombre' => 'Régimen Impositivo y Liquidación de Impuestos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 155, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 149, 'nombre' => 'Administración Financiera de Largo Plazo',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 156, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 150, 'nombre' => 'Fundamentos y Elementos de la Auditoría',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (IDs: 157 al 162) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 157, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 151, 'nombre' => 'Contabilidad de Entidades Financieras y Seguros',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 158, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 151, 'nombre' => 'Contabilidad Gubernamental e Integrada',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 159, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 151, 'nombre' => 'Contabilidad Agropecuaria y de Extractivas',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 160, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 154, 'nombre' => 'Régimen Impositivo y Liquidación de Impuestos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 161, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 155, 'nombre' => 'Análisis y Formulación de Presupuestos Empresariales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 162, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 156, 'nombre' => 'Auditoría Financiera I (Normas de Auditoría NIA)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (IDs: 163 al 168) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 163, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 157, 'nombre' => 'Contabilidad Avanzada II (Casos Internacionales)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 164, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 158, 'nombre' => 'Auditoría Gubernamental y Control Fiscal',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 165, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 160, 'nombre' => 'Auditoría Tributaria e Impositiva',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 166, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 161, 'nombre' => 'Preparación y Evaluación de Proyectos de Inversión',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 167, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 162, 'nombre' => 'Auditoría Financiera II (Papeles de Trabajo)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 168, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 161, 'nombre' => 'Taller de Grado I (Metodología)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (IDs: 164 al 174) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 169, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 167, 'nombre' => 'Auditoría Operativa y de Sistemas Contables',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 170, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 167, 'nombre' => 'Auditoría Forense (Prevención de Fraudes)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 171, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 163, 'nombre' => 'Análisis de Mercado de Capitales e Inversión',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 172, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 166, 'nombre' => 'Gerencia Estratégica y Dirección Financiera',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 173, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 168, 'nombre' => 'Taller de Grado II (Defensa y Simulación Profesional)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 174, 'idCarrera' => 3, 'idPensum' => 3, 'idMateriaPrevia' => 167, 'nombre' => 'Deontología Profesional y Ética del Auditor',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}