<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaDerechoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('materias')->insert([
            // =========================================================================
            // 0. MATERIAS ELECTIVAS (IDs: DER-001 al DER-006) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'DER-001', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: Derecho Informático y de las TICs',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-002', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Criminología y Política Criminal',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-003', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Derecho de la Propiedad Intelectual',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-004', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Métodos Alternativos de Resolución de Conflictos (MASC)',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-005', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Derecho Aduanero y Comercio Exterior',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-006', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Bioética y Derecho Sanitario',
                'semestre' => 'Electiva', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (IDs: DER-007 al DER-012) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 'DER-007', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Introducción al Derecho',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-008', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Derecho Romano e Historia del Derecho',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-009', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Sociología Jurídica',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-010', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Derecho Civil I (Personas y Derechos Reales)',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-011', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Ciencia Política',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-012', 'idCarrera' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Oratoria Jurídica y Expresión Escrita',
                'semestre' => '1', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (IDs: DER-013 al DER-018) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-013', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-007', 'nombre' => 'Derecho Constitucional I',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-014', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-007', 'nombre' => 'Derecho Penal I (Parte General)',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-015', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-010', 'nombre' => 'Derecho Civil II (Obligaciones)',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-016', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-009', 'nombre' => 'Antropología Jurídica',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-017', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-011', 'nombre' => 'Teoría del Estado',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-018', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-012', 'nombre' => 'Metodología de la Investigación Jurídica',
                'semestre' => '2', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (IDs: DER-019 al DER-024) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-019', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-013', 'nombre' => 'Derecho Constitucional II y Derechos Humanos',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-020', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-014', 'nombre' => 'Derecho Penal II (Parte Especial)',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-021', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-015', 'nombre' => 'Derecho Civil III (Contratos)',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-022', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-013', 'nombre' => 'Derecho Administrativo I',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-023', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-017', 'nombre' => 'Derecho Internacional Público',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-024', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-013', 'nombre' => 'Derecho de Familia y de las Sucesiones',
                'semestre' => '3', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (IDs: DER-025 al DER-030) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-025', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-019', 'nombre' => 'Garantías Constitucionales y Justicia Plurinacional',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-026', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-020', 'nombre' => 'Derecho Procesal Penal I',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-027', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-021', 'nombre' => 'Derecho Procesal Civil I',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-028', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-022', 'nombre' => 'Derecho Administrativo II y Procedimientos',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-029', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-021', 'nombre' => 'Derecho Comercial e Instituciones Mercantiles',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-030', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-024', 'nombre' => 'Derecho del Niño, Niña y Adolescente',
                'semestre' => '4', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (IDs: DER-031 al DER-036) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-031', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-026', 'nombre' => 'Derecho Procesal Penal II',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-032', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-027', 'nombre' => 'Derecho Procesal Civil II',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-033', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-029', 'nombre' => 'Derecho Corporativo y Títulos Valores',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-034', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-028', 'nombre' => 'Derecho Laboral y Relaciones de Trabajo',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-035', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-028', 'nombre' => 'Derecho Autonómico y Municipal',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-036', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-025', 'nombre' => 'Derecho de los Pueblos Indígenas Originarios',
                'semestre' => '5', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (IDs: DER-037 al DER-042) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-037', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-032', 'nombre' => 'Práctica Forense Civil I',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-038', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-031', 'nombre' => 'Práctica Forense Penal I',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-039', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-034', 'nombre' => 'Derecho Procesal del Trabajo y Seguridad Social',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-040', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-033', 'nombre' => 'Derecho Tributario y Financiero',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-041', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-033', 'nombre' => 'Derecho Minero, de Hidrocarburos y de la Energía',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-042', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-036', 'nombre' => 'Derecho Agrario, Ambiental y de Tierras',
                'semestre' => '6', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (IDs: DER-043 al DER-048) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-043', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-037', 'nombre' => 'Práctica Forense Civil II (Taller de Litigación)',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-044', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-038', 'nombre' => 'Práctica Forense Penal II (Juicio Oral)',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-045', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-040', 'nombre' => 'Derecho Procesal Tributario',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-046', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-023', 'nombre' => 'Derecho Internacional Privado',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-047', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-038', 'nombre' => 'Medicina Legal y Ciencias Forenses',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-048', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-037', 'nombre' => 'Derecho Notarial y Registral',
                'semestre' => '7', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (IDs: DER-049 al DER-054) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-049', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-043', 'nombre' => 'Clínica Jurídica y Consultorio Popular I',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-050', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-044', 'nombre' => 'Técnicas de Argumentación e Interpretación Jurídica',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-051', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-046', 'nombre' => 'Derecho de la Integración y Tratados Internacionales',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-052', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-048', 'nombre' => 'Derecho Bancario, de Seguros y Mercado de Valores',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-053', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-045', 'nombre' => 'Derecho Procesal Orgánico y de la Magistratura',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-054', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-018', 'nombre' => 'Taller de Grado I',
                'semestre' => '8', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 9º SEMESTRE (IDs: DER-055 al DER-060) - Requieren del 8º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-055', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-049', 'nombre' => 'Clínica Jurídica y Consultorio Popular II',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-056', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-050', 'nombre' => 'Filosofía del Derecho y Teoría Jurídica',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-057', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-052', 'nombre' => 'Derecho de Ejecución Penal y Régimen Penitenciario',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-058', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-052', 'nombre' => 'Derecho de la Competencia y Defensa del Consumidor',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-059', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-051', 'nombre' => 'Sistemas Internacionales de Protección de DDHH',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-060', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-054', 'nombre' => 'Taller de Grado II',
                'semestre' => '9', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 10º SEMESTRE (IDs: DER-061 al DER-066) - Requieren del 9º Semestre
            // =========================================================================
            [
                'idMateria' => 'DER-061', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-055', 'nombre' => 'Práctica Externa Dirigida (Pasantía Profesional)',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-062', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-056', 'nombre' => 'Deontología Jurídica y Ética Profesional',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-063', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-059', 'nombre' => 'Derecho Internacional Humanitario',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-064', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-058', 'nombre' => 'Derecho Electoral y de los Partidos Políticos',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-065', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-057', 'nombre' => 'Derecho Procesal Constitucional Avanzado',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 'DER-066', 'idCarrera' => 4, 'idMateriaPrevia' => 'DER-060', 'nombre' => 'Proyecto de Grado / Memoria de Licenciatura',
                'semestre' => '10', 'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}