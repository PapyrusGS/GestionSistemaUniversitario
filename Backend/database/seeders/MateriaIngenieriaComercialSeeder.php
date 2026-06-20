<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaIngenieriaComercialSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materias')->insert([
            // =========================================================================
            // 0. MATERIAS ELECTIVAS (COM-001 al COM-006) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'COM-001', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: Growth Hacking y Analítica Digital',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-002', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Gestión del Franquiciamiento y Expansión',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-003', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Modelos de Negocios Disruptivos',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-004', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Dirección de Ventas de Alto Rendimiento',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-005', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Logística Internacional y Supply Chain',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-006', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Negociación Intercultural Avanzada',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (COM-007 al COM-012) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'COM-007', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Introducción a la Ingeniería Comercial',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-008', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Cálculo I',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-009', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Álgebra Lineal',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-010', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Contabilidad General',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-011', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Microeconomía I',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-012', 'idCarrera' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Metodología de la Investigación y Comunicación',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (COM-013 al COM-018) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 'COM-013', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-007', 'nombre' => 'Administración General y Procesos',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-014', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-008', 'nombre' => 'Cálculo II',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-015', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-010', 'nombre' => 'Contabilidad de Costos',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-016', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-011', 'nombre' => 'Microeconomía II',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-017', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-011', 'nombre' => 'Macroeconomía I',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-018', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-009', 'nombre' => 'Estadística Descriptiva',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (COM-019 al COM-024) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 'COM-019', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-013', 'nombre' => 'Mercadotecnia I',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-020', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-014', 'nombre' => 'Matemática Financiera',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-021', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-013', 'nombre' => 'Comportamiento del Consumidor',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-022', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-017', 'nombre' => 'Macroeconomía II',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-023', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-018', 'nombre' => 'Estadística Inferencial',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-024', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-013', 'nombre' => 'Derecho Comercial y Laboral',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (COM-025 al COM-030) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 'COM-025', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-019', 'nombre' => 'Mercadotecnia II',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-026', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-020', 'nombre' => 'Finanzas de Empresas I',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-027', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-023', 'nombre' => 'Investigación de Mercados I',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-028', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-023', 'nombre' => 'Investigación Operativa',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-029', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-022', 'nombre' => 'Economía Internacional y de Integración',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-030', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-019', 'nombre' => 'Sistemas de Información Comercial',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (COM-031 al COM-036) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 'COM-031', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-025', 'nombre' => 'Estrategia de Precios y Distribución',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-032', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-026', 'nombre' => 'Finanzas de Empresas II',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-033', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-027', 'nombre' => 'Investigación de Mercados II',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-034', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-028', 'nombre' => 'Econometría I',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-035', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-025', 'nombre' => 'Marketing de Servicios y Relacional',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-036', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-026', 'nombre' => 'Presupuestos y Control Comercial',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (COM-037 al COM-042) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 'COM-037', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-031', 'nombre' => 'Gerencia de Producto y Branding',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-038', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-032', 'nombre' => 'Ingeniería Financiera',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-039', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-033', 'nombre' => 'Trade Marketing y Gestión del Retail',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-040', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-034', 'nombre' => 'Econometría II',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-041', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-035', 'nombre' => 'Marketing Digital y Social Media',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-042', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-036', 'nombre' => 'Preparación y Evaluación de Proyectos I',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (COM-043 al COM-048) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 'COM-043', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-037', 'nombre' => 'Dirección Estratégica Corporativa',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-044', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-037', 'nombre' => 'Marketing Internacional y Globalización',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-045', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-041', 'nombre' => 'Business Intelligence (Inteligencia de Negocios)',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-046', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-041', 'nombre' => 'E-Commerce y Canales Digitales de Venta',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-047', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-042', 'nombre' => 'Preparación y Evaluación de Proyectos II',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-048', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-040', 'nombre' => 'Taller de Grado I',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (COM-049 al COM-054) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 'COM-049', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-043', 'nombre' => 'Simulación de Negocios y Juegos de Estrategia',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-050', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-043', 'nombre' => 'Creación de Empresas y Planes de Negocio',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-051', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-045', 'nombre' => 'Big Data Aplicado al Marketing Comercial',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-052', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-044', 'nombre' => 'Auditoría de Marketing y Comercialización',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-053', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-048', 'nombre' => 'Taller de Grado II (Defensa de Proyecto)',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'COM-054', 'idCarrera' => 5, 'idMateriaPrevia' => 'COM-043', 'nombre' => 'Ética Profesional y Deontología Comercial',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}