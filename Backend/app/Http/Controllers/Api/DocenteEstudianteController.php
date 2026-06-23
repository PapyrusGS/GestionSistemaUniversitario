<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocenteEstudianteController extends Controller
{
    /**
     * Recupera los estudiantes inscritos en base al idCursoMateria de la nueva BD.
     */
    public function obtenerEstudiantesPorCursoMateria(Request $request)
    {
        try {
            // Validamos que el frontend envíe de forma obligatoria el idCursoMateria
            $request->validate([
                'idCursoMateria' => 'required'
            ]);

            $idCursoMateria = $request->input('idCursoMateria');

            // Consulta SQL estructurada con los nombres exactos de tu script de base de datos
            $estudiantes = DB::table('estudiantemateria')
                ->join('cursos_materias', 'estudiantemateria.idCursoMateria', '=', 'cursos_materias.idCursoMateria')
                ->join('estudiante', 'estudiantemateria.idEstudiante', '=', 'estudiante.idEstudiante')
                ->join('usuarios', 'estudiante.idUsuario', '=', 'usuarios.idUsuario')
                ->select(
                    'estudiantemateria.idInscripcion',
                    'estudiantemateria.idCursoMateria',
                    'estudiante.idEstudiante',
                    'usuarios.idUsuario',
                    'usuarios.name AS nombre_estudiante',
                    'usuarios.apellidoP AS apellido_paterno',
                    'usuarios.apellidoM AS apellido_materno',
                    'usuarios.email',
                    'usuarios.ci',
                    'estudiantemateria.estado AS estado_inscripcion'
                )
                ->where('estudiantemateria.idCursoMateria', $idCursoMateria)
                ->get();

            // Retorna la colección en formato JSON para que Vue pueda recorrerla con v-for
            return response()->json([
                'success' => true,
                'data' => $estudiantes,
                'message' => 'Estudiantes recuperados correctamente.'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error crítico en DocenteEstudianteController: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema en el servidor al obtener la lista de alumnos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}