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
            // 0. MATERIAS ELECTIVAS (IDs: 67 al 72) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 67, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: E-Commerce y Negocios Digitales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 68, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Coaching y Liderazgo Organizacional',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 69, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Neuromarketing',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 70, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Responsabilidad Social Empresarial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 71, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Gestión de la Innovación',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 72, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Franquicias y Modelos de Expansión',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (IDs: 73 al 78) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 73, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Administración I (Principios de Administración)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 74, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Contabilidad General I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 75, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Matemática Financiera I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 76, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Introducción al Derecho Económico',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 77, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Microeconomía I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 78, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => null, 'nombre' => 'Técnicas de Estudio e Investigación',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (IDs: 79 al 84) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 79, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 73, 'nombre' => 'Administración II (Proceso Administrativo)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 80, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 74, 'nombre' => 'Contabilidad General II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 81, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 75, 'nombre' => 'Matemática Financiera II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 82, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 76, 'nombre' => 'Derecho Comercial y Societario',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 83, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 77, 'nombre' => 'Microeconomía II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 84, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 78, 'nombre' => 'Estadística Descriptiva',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (IDs: 85 al 90) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 85, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 79, 'nombre' => 'Comportamiento Organizacional',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 86, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 80, 'nombre' => 'Contabilidad de Costos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 87, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 81, 'nombre' => 'Finanzas de Empresas I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 88, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 82, 'nombre' => 'Derecho Laboral y Seguridad Social',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 89, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 83, 'nombre' => 'Macroeconomía I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 90, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 84, 'nombre' => 'Estadística Inferencial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (IDs: 91 al 96) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 91, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 85, 'nombre' => 'Administración de Recursos Humanos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 92, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 86, 'nombre' => 'Contabilidad de Costos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 93, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 87, 'nombre' => 'Finanzas de Empresas II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 94, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 85, 'nombre' => 'Mercadotecnia I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 95, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 89, 'nombre' => 'Macroeconomía II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 96, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 90, 'nombre' => 'Investigación de Operaciones',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (IDs: 97 al 102) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 97, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 91, 'nombre' => 'Administración de Recursos Humanos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 98, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 93, 'nombre' => 'Análisis e Interpretación de Estados Financieros',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 99, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 94, 'nombre' => 'Mercadotecnia II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 100, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 91, 'nombre' => 'Administración de la Producción I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 101, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 94, 'nombre' => 'Investigación de Mercados',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 102, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 95, 'nombre' => 'Comercio Internacional',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (IDs: 103 al 108) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 103, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 97, 'nombre' => 'Diseño y Estructuras Organizacionales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 104, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 98, 'nombre' => 'Presupuestos y Control de Gestión',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 105, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 99, 'nombre' => 'Estrategias de Precios y Distribución',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 106, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 100, 'nombre' => 'Administración de la Producción II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 107, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 97, 'nombre' => 'Sistemas de Información Gerencial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 108, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 102, 'nombre' => 'Preparación y Evaluación de Proyectos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (IDs: 109 al 114) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 109, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 103, 'nombre' => 'Dirección Estratégica I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 110, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 104, 'nombre' => 'Ingeniería Financiera',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 111, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 105, 'nombre' => 'Gerencia de Marca y Producto',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 112, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 106, 'nombre' => 'Logística y Cadena de Suministros',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 113, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 107, 'nombre' => 'Taller de Grado I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 114, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 108, 'nombre' => 'Preparación y Evaluación de Proyectos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (IDs: 115 al 120) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 115, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 109, 'nombre' => 'Dirección Estratégica II (Simulación Empresarial)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 116, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 110, 'nombre' => 'Mercado de Valores y Capitales',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 117, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 111, 'nombre' => 'Auditoría Administrativa',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 118, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 109, 'nombre' => 'Creación de Empresas (Emprendimiento)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 119, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 113, 'nombre' => 'Taller de Grado II (Defensa de Proyecto)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 120, 'idCarrera' => 2, 'idPensum' => 2, 'idMateriaPrevia' => 109, 'nombre' => 'Ética de los Negocios y Deontología Empresarial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}