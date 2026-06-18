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
            // 0. MATERIAS ELECTIVAS (IDs: 175 al 180) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 175, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: Derecho Informático y de las TICs',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 176, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Criminología y Política Criminal',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 177, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Derecho de la Propiedad Intelectual',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 178, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Métodos Alternativos de Resolución de Conflictos (MASC)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 179, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Derecho Aduanero y Comercio Exterior',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 180, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Bioética y Derecho Sanitario',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (IDs: 181 al 186) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 181, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Introducción al Derecho',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 182, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Derecho Romano e Historia del Derecho',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 183, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Sociología Jurídica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 184, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Derecho Civil I (Personas y Derechos Reales)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 185, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Ciencia Política',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 186, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => null, 'nombre' => 'Oratoria Jurídica y Expresión Escrita',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (IDs: 187 al 192) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 187, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 181, 'nombre' => 'Derecho Constitucional I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 188, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 181, 'nombre' => 'Derecho Penal I (Parte General)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 189, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 184, 'nombre' => 'Derecho Civil II (Obligaciones)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 190, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 183, 'nombre' => 'Antropología Jurídica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 191, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 185, 'nombre' => 'Teoría del Estado',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 192, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 186, 'nombre' => 'Metodología de la Investigación Jurídica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (IDs: 193 al 198) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 193, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 187, 'nombre' => 'Derecho Constitucional II y Derechos Humanos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 194, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 188, 'nombre' => 'Derecho Penal II (Parte Especial)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 195, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 189, 'nombre' => 'Derecho Civil III (Contratos)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 196, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 187, 'nombre' => 'Derecho Administrativo I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 197, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 191, 'nombre' => 'Derecho Internacional Público',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 198, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 187, 'nombre' => 'Derecho de Familia y de las Sucesiones',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (IDs: 199 al 204) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 199, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 193, 'nombre' => 'Garantías Constitucionales y Justicia Plurinacional',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 200, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 194, 'nombre' => 'Derecho Procesal Penal I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 201, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 195, 'nombre' => 'Derecho Procesal Civil I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 202, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 196, 'nombre' => 'Derecho Administrativo II y Procedimientos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 203, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 195, 'nombre' => 'Derecho Comercial e Instituciones Mercantiles',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 204, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 198, 'nombre' => 'Derecho del Niño, Niña y Adolescente',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (IDs: 205 al 210) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 205, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 200, 'nombre' => 'Derecho Procesal Penal II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 206, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 201, 'nombre' => 'Derecho Procesal Civil II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 207, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 203, 'nombre' => 'Derecho Corporativo y Títulos Valores',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 208, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 202, 'nombre' => 'Derecho Laboral y Relaciones de Trabajo',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 209, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 202, 'nombre' => 'Derecho Autonómico y Municipal',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 210, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 199, 'nombre' => 'Derecho de los Pueblos Indígenas Originarios',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (IDs: 211 al 216) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 211, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 206, 'nombre' => 'Práctica Forense Civil I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 212, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 205, 'nombre' => 'Práctica Forense Penal I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 213, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 208, 'nombre' => 'Derecho Procesal del Trabajo y Seguridad Social',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 214, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 207, 'nombre' => 'Derecho Tributario y Financiero',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 215, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 207, 'nombre' => 'Derecho Minero, de Hidrocarburos y de la Energía',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 216, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 210, 'nombre' => 'Derecho Agrario, Ambiental y de Tierras',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (IDs: 217 al 222) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 217, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 211, 'nombre' => 'Práctica Forense Civil II (Taller de Litigación)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 218, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 212, 'nombre' => 'Práctica Forense Penal II (Juicio Oral)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 219, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 214, 'nombre' => 'Derecho Procesal Tributario',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 220, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 197, 'nombre' => 'Derecho Internacional Privado',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 221, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 212, 'nombre' => 'Medicina Legal y Ciencias Forenses',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 222, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 211, 'nombre' => 'Derecho Notarial y Registral',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (IDs: 223 al 228) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 223, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 217, 'nombre' => 'Clínica Jurídica y Consultorio Popular I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 224, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 218, 'nombre' => 'Técnicas de Argumentación e Interpretación Jurídica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 225, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 220, 'nombre' => 'Derecho de la Integración y Tratados Internacionales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 226, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 222, 'nombre' => 'Derecho Bancario, de Seguros y Mercado de Valores',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 227, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 219, 'nombre' => 'Derecho Procesal Orgánico y de la Magistratura',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 228, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 192, 'nombre' => 'Taller de Grado I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 9º SEMESTRE (IDs: 229 al 234) - Requieren del 8º Semestre
            // =========================================================================
            [
                'idMateria' => 229, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 223, 'nombre' => 'Clínica Jurídica y Consultorio Popular II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 230, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 224, 'nombre' => 'Filosofía del Derecho y Teoría Jurídica',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 231, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 226, 'nombre' => 'Derecho de Ejecución Penal y Régimen Penitenciario',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 232, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 226, 'nombre' => 'Derecho de la Competencia y Defensa del Consumidor',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 233, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 225, 'nombre' => 'Sistemas Internacionales de Protección de DDHH',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 234, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 228, 'nombre' => 'Taller de Grado II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 10º SEMESTRE (IDs: 235 al 240) - Requieren del 9º Semestre
            // =========================================================================
            [
                'idMateria' => 235, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 229, 'nombre' => 'Práctica Externa Dirigida (Pasantía Profesional)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 236, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 230, 'nombre' => 'Deontología Jurídica y Ética Profesional',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 237, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 233, 'nombre' => 'Derecho Internacional Humanitario',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 238, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 232, 'nombre' => 'Derecho Electoral y de los Partidos Políticos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 239, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 231, 'nombre' => 'Derecho Procesal Constitucional Avanzado',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 240, 'idCarrera' => 4, 'idPensum' => 4, 'idMateriaPrevia' => 234, 'nombre' => 'Proyecto de Grado / Memoria de Licenciatura',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}