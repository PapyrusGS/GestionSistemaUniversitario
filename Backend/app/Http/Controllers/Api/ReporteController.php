<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CursoMateria;
use App\Models\Inscripcion;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\User;
use App\Exports\ReporteExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /**
     * Devuelve las listas de filtros disponibles (Cursos y Materias)
     */
    public function filtros(): JsonResponse
    {
        $cursos = Curso::where('estado', 1)->get(['idCurso', 'idCurso as nombre']);
        $materias = Materia::where('estado', 1)->get(['idMateria', 'nombre', 'semestre']);

        return response()->json([
            'cursos' => $cursos,
            'materias' => $materias
        ]);
    }

    /**
     * Construye los datos y encabezados para los reportes dinámicos
     */
    private function getReportData(Request $request)
    {
        $tipo = $request->query('tipo', 'inscripciones'); // inscripciones, docentes, materias, cursos
        $idCurso = $request->query('curso');
        $idMateria = $request->query('materia');

        if ($tipo === 'docentes') {
            $query = User::where('estado', 1)->whereHas('rol', function($q) {
                $q->where('nombre', 'Docente');
            });
            $docentes = $query->get();
            $headings = ['Nombre Completo', 'CI', 'Correo'];
            $data = $docentes->map(function ($d) {
                return [
                    $d->nombreCompleto,
                    $d->ci,
                    $d->correo,
                ];
            });
            return ['headings' => $headings, 'data' => $data];
        }

        if ($tipo === 'materias') {
            $materias = Materia::where('estado', 1)->get();
            $headings = ['ID Materia', 'Nombre', 'Semestre'];
            $data = $materias->map(function ($m) {
                return [
                    $m->idMateria,
                    $m->nombre,
                    $m->semestre,
                ];
            });
            return ['headings' => $headings, 'data' => $data];
        }

        if ($tipo === 'cursos') {
            $query = CursoMateria::with(['curso', 'materia', 'docente'])->where('estado', 1);
            if ($idCurso) $query->where('idCurso', $idCurso);
            if ($idMateria) $query->where('idMateria', $idMateria);
            
            $cursos = $query->get();
            $headings = ['ID Curso', 'Materia', 'Docente', 'Capacidad (Aula)', 'Max Inscritos'];
            $data = $cursos->map(function ($c) {
                $doc = $c->docente;
                return [
                    $c->idCurso,
                    $c->materia ? $c->materia->nombre : 'N/A',
                    $doc ? $doc->nombreCompleto : 'Sin Asignar',
                    $c->curso ? $c->curso->capacidad : 'N/A',
                    $c->maxInscritos ?? 'N/A',
                ];
            });
            return ['headings' => $headings, 'data' => $data];
        }

        // Default: Inscripciones / Notas
        $query = Inscripcion::with([
            'estudiante',
            'cursoMateria.curso',
            'cursoMateria.materia',
            'notaMasReciente'
        ])->where('estado', 1);

        if ($idCurso || $idMateria) {
            $query->whereHas('cursoMateria', function ($q) use ($idCurso, $idMateria) {
                if ($idCurso) {
                    $q->where('idCurso', $idCurso);
                }
                if ($idMateria) {
                    $q->where('idMateria', $idMateria);
                }
            });
        }

        $inscripciones = $query->get();
        $headings = ['Estudiante', 'CI', 'Curso', 'Materia', 'Nota'];
        $data = $inscripciones->map(function ($inscripcion) {
            $estudiante = $inscripcion->estudiante;
            $cm = $inscripcion->cursoMateria;
            return [
                $estudiante ? collect([
                    $estudiante->nombre1, $estudiante->nombre2, 
                    $estudiante->apellido1, $estudiante->apellido2
                ])->filter()->implode(' ') : 'Desconocido',
                $estudiante ? $estudiante->ci : 'N/A',
                $cm && $cm->curso ? $cm->curso->idCurso : 'N/A',
                $cm && $cm->materia ? $cm->materia->nombre : 'N/A',
                $inscripcion->notaMasReciente ? $inscripcion->notaMasReciente->nota : 'N/A',
            ];
        });

        return ['headings' => $headings, 'data' => $data];
    }

    public function index(Request $request): JsonResponse
    {
        $payload = $this->getReportData($request);

        if ($payload['data']->isEmpty()) {
            return response()->json([
                'message' => 'No hay información disponible',
                'data' => [],
                'headings' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Reporte generado correctamente',
            'headings' => $payload['headings'],
            'data' => $payload['data']
        ]);
    }

    public function exportar(Request $request)
    {
        $payload = $this->getReportData($request);
        
        if ($payload['data']->isEmpty()) {
            return response()->json([
                'message' => 'No hay información disponible para exportar'
            ], 404);
        }

        $formato = $request->query('formato', 'pdf');

        if ($formato === 'excel') {
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'reporte.xlsx');
        }

        // Por defecto, exportar a PDF
        $pdf = Pdf::loadView('reportes.pdf', [
            'headings' => $payload['headings'],
            'data' => $payload['data']
        ]);
        return $pdf->download('reporte.pdf');
    }
}
