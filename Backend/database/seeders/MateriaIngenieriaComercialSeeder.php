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
            // 0. MATERIAS ELECTIVAS (IDs: 241 al 246) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 241, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva I: Growth Hacking y Analítica Digital',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 242, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva II: Gestión del Franquiciamiento y Expansión',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 243, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva III: Modelos de Negocios Disruptivos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 244, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva IV: Dirección de Ventas de Alto Rendimiento',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 245, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva V: Logística Internacional y Supply Chain',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 246, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Electiva VI: Negociación Intercultural Avanzada',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 1º SEMESTRE (IDs: 247 al 252) - Sin prerrequisitos
            // =========================================================================
            [
                'idMateria' => 247, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Introducción a la Ingeniería Comercial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 248, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Cálculo I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 249, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Álgebra Lineal',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 250, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Contabilidad General',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 251, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Microeconomía I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 252, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => null, 'nombre' => 'Metodología de la Investigación y Comunicación',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 2º SEMESTRE (IDs: 253 al 258) - Requieren del 1º Semestre
            // =========================================================================
            [
                'idMateria' => 253, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 247, 'nombre' => 'Administración General y Procesos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 254, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 248, 'nombre' => 'Cálculo II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 255, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 250, 'nombre' => 'Contabilidad de Costos',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 256, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 251, 'nombre' => 'Microeconomía II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 257, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 251, 'nombre' => 'Macroeconomía I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 258, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 249, 'nombre' => 'Estadística Descriptiva',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 3º SEMESTRE (IDs: 259 al 264) - Requieren del 2º Semestre
            // =========================================================================
            [
                'idMateria' => 259, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 253, 'nombre' => 'Mercadotecnia I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 260, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 254, 'nombre' => 'Matemática Financiera',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 261, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 253, 'nombre' => 'Comportamiento del Consumidor',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 262, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 257, 'nombre' => 'Macroeconomía II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 263, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 258, 'nombre' => 'Estadística Inferencial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 264, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 253, 'nombre' => 'Derecho Comercial y Laboral',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 4º SEMESTRE (IDs: 265 al 270) - Requieren del 3º Semestre
            // =========================================================================
            [
                'idMateria' => 265, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 259, 'nombre' => 'Mercadotecnia II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 266, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 260, 'nombre' => 'Finanzas de Empresas I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 267, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 263, 'nombre' => 'Investigación de Mercados I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 268, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 263, 'nombre' => 'Investigación Operativa',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 269, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 262, 'nombre' => 'Economía Internacional y de Integración',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 270, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 259, 'nombre' => 'Sistemas de Información Comercial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 5º SEMESTRE (IDs: 271 al 276) - Requieren del 4º Semestre
            // =========================================================================
            [
                'idMateria' => 271, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 265, 'nombre' => 'Estrategia de Precios y Distribución',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 272, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 266, 'nombre' => 'Finanzas de Empresas II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 273, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 267, 'nombre' => 'Investigación de Mercados II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 274, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 268, 'nombre' => 'Econometría I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 275, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 265, 'nombre' => 'Marketing de Servicios y Relacional',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 276, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 266, 'nombre' => 'Presupuestos y Control Comercial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 6º SEMESTRE (IDs: 277 al 282) - Requieren del 5º Semestre
            // =========================================================================
            [
                'idMateria' => 277, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 271, 'nombre' => 'Gerencia de Producto y Branding',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 278, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 272, 'nombre' => 'Ingeniería Financiera',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 279, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 273, 'nombre' => 'Trade Marketing y Gestión del Retail',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 280, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 274, 'nombre' => 'Econometría II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 281, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 275, 'nombre' => 'Marketing Digital y Social Media',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 282, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 276, 'nombre' => 'Preparación y Evaluación de Proyectos I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 7º SEMESTRE (IDs: 283 al 288) - Requieren del 6º Semestre
            // =========================================================================
            [
                'idMateria' => 283, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 277, 'nombre' => 'Dirección Estratégica Corporativa',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 284, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 277, 'nombre' => 'Marketing Internacional y Globalización',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 285, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 281, 'nombre' => 'Business Intelligence (Inteligencia de Negocios)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 286, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 281, 'nombre' => 'E-Commerce y Canales Digitales de Venta',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 287, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 282, 'nombre' => 'Preparación y Evaluación de Proyectos II',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 288, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 280, 'nombre' => 'Taller de Grado I',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],

            // =========================================================================
            // 8º SEMESTRE (IDs: 289 al 294) - Requieren del 7º Semestre
            // =========================================================================
            [
                'idMateria' => 289, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 283, 'nombre' => 'Simulación de Negocios y Juegos de Estrategia',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 290, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 283, 'nombre' => 'Creación de Empresas y Planes de Negocio',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 291, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 285, 'nombre' => 'Big Data Aplicado al Marketing Comercial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 292, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 284, 'nombre' => 'Auditoría de Marketing y Comercialización',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 293, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 288, 'nombre' => 'Taller de Grado II (Defensa de Proyecto)',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'idMateria' => 294, 'idCarrera' => 5, 'idPensum' => 5, 'idMateriaPrevia' => 283, 'nombre' => 'Ética Profesional y Deontología Comercial',
                'fechaRegistro' => now(), 'estado' => true, 'fechaA' => now(), 'UsuarioA' => '1', 'estadoA' => true, 'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}