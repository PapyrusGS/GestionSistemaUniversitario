<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\Carrera;
use App\Models\Curso;
use App\Models\CursoMateria;
use App\Models\Estudiante;
use App\Models\EstudianteCarrera;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\User;
use App\Exports\ReporteExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // DATOS DE FILTROS (dropdown options para el frontend)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve las listas de filtros disponibles.
     * Extiende la respuesta original con `periodos`.
     */
    public function filtros(): JsonResponse
    {
        $cursos   = Curso::where('estado', 1)->get(['idCurso', 'idCurso as nombre']);
        $materias = Materia::where('estado', 1)->get(['idMateria', 'nombre', 'semestre']);
        $carreras = Carrera::where('estado', 1)->get(['idCarrera', 'nombre']);
        $periodos = Periodo::where('estado', 1)->orderByDesc('idPeriodo')->get(['idPeriodo', 'nombre']);

        return response()->json([
            'cursos'   => $cursos,
            'materias' => $materias,
            'carreras' => $carreras,
            'periodos' => $periodos,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE ORIGINAL (conservado intacto)
    // ──────────────────────────────────────────────────────────────────────────

    private function getReportData(Request $request): array
    {
        $tipo      = $request->query('tipo', 'inscripciones');
        $idCurso   = $request->query('curso');
        $idMateria = $request->query('materia');
        $idCarrera = $request->query('carrera');
        $semestre  = $request->query('semestre');

        if ($tipo === 'docentes') {
            $docentes = User::where('estado', 1)
                ->whereHas('rol', fn ($q) => $q->where('nombre', 'Docente'))
                ->get();
            $headings = ['Nombre Completo', 'CI', 'Correo'];
            $data = $docentes->map(fn ($d) => [$d->nombreCompleto, $d->ci, $d->correo]);
            return ['headings' => $headings, 'data' => $data];
        }

        if ($tipo === 'materias') {
            $query = Materia::with('carrera')->where('estado', 1);
            if ($idCarrera) $query->where('idCarrera', $idCarrera);
            if ($semestre)  $query->where('semestre', (int) $semestre);
            $materias = $query->get();
            $headings = ['ID Materia', 'Nombre', 'Semestre', 'Carrera'];
            $data = $materias->map(fn ($m) => [
                $m->idMateria,
                $m->nombre,
                $m->semestre,
                $m->carrera?->nombre ?? 'N/A',
            ]);
            return ['headings' => $headings, 'data' => $data];
        }

        if ($tipo === 'cursos') {
            $query = CursoMateria::with(['curso', 'materia', 'docente'])->where('estado', 1);
            if ($idCurso)   $query->where('idCurso', $idCurso);
            if ($idMateria) $query->where('idMateria', $idMateria);
            $cursos   = $query->get();
            $headings = ['ID Curso', 'Materia', 'Docente', 'Capacidad (Aula)', 'Max Inscritos'];
            $data = $cursos->map(fn ($c) => [
                $c->idCurso,
                $c->materia?->nombre          ?? 'N/A',
                $c->docente?->nombreCompleto   ?? 'Sin Asignar',
                $c->curso?->capacidad          ?? 'N/A',
                $c->maxInscritos               ?? 'N/A',
            ]);
            return ['headings' => $headings, 'data' => $data];
        }

        // Default: inscripciones / notas
        $query = Inscripcion::with([
            'estudiante',
            'cursoMateria.curso',
            'cursoMateria.materia',
            'notaMasReciente',
        ])->where('estado', 1);

        if ($idCurso || $idMateria) {
            $query->whereHas('cursoMateria', function ($q) use ($idCurso, $idMateria) {
                if ($idCurso)   $q->where('idCurso', $idCurso);
                if ($idMateria) $q->where('idMateria', $idMateria);
            });
        }

        $inscripciones = $query->get();
        $headings = ['Estudiante', 'CI', 'Curso', 'Materia', 'Nota'];
        $data = $inscripciones->map(function ($i) {
            $est = $i->estudiante;
            $cm  = $i->cursoMateria;
            return [
                $est ? collect([$est->nombre1, $est->nombre2, $est->apellido1, $est->apellido2])->filter()->implode(' ') : 'Desconocido',
                $est?->ci                           ?? 'N/A',
                $cm?->curso?->idCurso               ?? 'N/A',
                $cm?->materia?->nombre              ?? 'N/A',
                $i->notaMasReciente?->nota           ?? 'N/A',
            ];
        });

        return ['headings' => $headings, 'data' => $data];
    }

    public function index(Request $request): JsonResponse
    {
        $payload = $this->getReportData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'No hay información disponible', 'data' => [], 'headings' => []], 200);
        }
        return response()->json(['message' => 'Reporte generado correctamente', 'headings' => $payload['headings'], 'data' => $payload['data']]);
    }

    public function exportar(Request $request)
    {
        $payload = $this->getReportData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'No hay información disponible para exportar'], 404);
        }
        $formato = $request->query('formato', 'pdf');
        if ($formato === 'excel') {
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'reporte.xlsx');
        }
        $pdf = Pdf::loadView('reportes.pdf', ['headings' => $payload['headings'], 'data' => $payload['data']]);
        return $pdf->download('reporte.pdf');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 1: RENDIMIENTO ACADÉMICO
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Construye el dataset de rendimiento.
     * Usa selectRaw + withCount para evitar N+1 en promedio de notas.
     */
    private function buildRendimientoData(Request $request): array
    {
        $idPeriodo = $request->query('idPeriodo');
        $idCarrera = $request->query('idCarrera');

        $query = CursoMateria::query()
            ->with(['materia', 'docente', 'periodo'])
            ->withCount([
                'inscripciones as total_inscritos' => fn ($q) => $q->where('estado', 1),
            ])
            ->selectRaw('cursos_materias.*, (
                SELECT ROUND(AVG(n.nota), 2)
                FROM estudiantemateria em
                INNER JOIN notas n ON n.idInscripcion = em.idInscripcion
                WHERE em.idCursoMateria = cursos_materias.idCursoMateria
                  AND em.estado = 1
                  AND n.estado  = 1
            ) AS promedio_nota')
            ->where('cursos_materias.estado', 1);

        if ($idPeriodo) {
            $query->where('cursos_materias.idPeriodo', $idPeriodo);
        }
        if ($idCarrera) {
            $query->whereHas('materia', fn ($q) => $q->where('idCarrera', $idCarrera));
        }

        $resultados = $query->orderBy('cursos_materias.idPeriodo')->get();

        $headings = ['Materia', 'Semestre', 'Período', 'Docente', 'Total Inscritos', 'Promedio Nota'];
        $data = $resultados->map(fn ($cm) => [
            $cm->materia?->nombre         ?? 'N/A',
            $cm->materia?->semestre        ?? 'N/A',
            $cm->periodo?->nombre          ?? 'N/A',
            $cm->docente?->nombreCompleto  ?? 'Sin asignar',
            $cm->total_inscritos,
            $cm->promedio_nota !== null ? number_format((float) $cm->promedio_nota, 2) : 'Sin notas',
        ]);

        return ['headings' => $headings, 'data' => $data];
    }

    public function reporteRendimiento(Request $request): JsonResponse
    {
        $payload = $this->buildRendimientoData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'Sin resultados para los filtros aplicados.', 'headings' => $payload['headings'], 'data' => []], 200);
        }
        return response()->json(['headings' => $payload['headings'], 'data' => $payload['data']]);
    }

    public function exportarRendimiento(Request $request)
    {
        $payload = $this->buildRendimientoData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'Sin datos para exportar.'], 404);
        }
        if ($request->query('formato') === 'excel') {
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'rendimiento.xlsx');
        }
        $pdf = Pdf::loadView('reportes.rendimiento_pdf', ['headings' => $payload['headings'], 'data' => $payload['data']]);
        return $pdf->setPaper('a4', 'landscape')->download('rendimiento.pdf');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 2: KÁRDEX DE ESTUDIANTE
    // ──────────────────────────────────────────────────────────────────────────

    private function buildKardexData(string $ci): array|JsonResponse
    {
        $usuario = User::where('ci', $ci)->where('estado', 1)->first();
        if (!$usuario) {
            return response()->json(['message' => "No se encontró ningún usuario activo con CI: {$ci}"], 404);
        }

        $estudiante = Estudiante::where('idUsuario', $usuario->idUsuario)->first();
        if (!$estudiante) {
            return response()->json(['message' => 'El usuario no tiene perfil de estudiante registrado.'], 404);
        }

        // Carrera activa (Eager Loading evita N+1)
        $ec = EstudianteCarrera::with('carrera')
            ->where('idEstudiante', $estudiante->idEstudiante)
            ->where('estado', 1)
            ->first();

        // Historial completo con un solo query (Eager Loading)
        $inscripciones = Inscripcion::with([
            'cursoMateria.materia',
            'cursoMateria.periodo',
            'notaMasReciente',
        ])
        ->where('idEstudiante', $estudiante->idEstudiante)
        ->get();

        $historial = $inscripciones->map(function ($insc) {
            $cm   = $insc->cursoMateria;
            $nota = $insc->notaMasReciente?->nota;
            return [
                'periodo'         => $cm?->periodo?->nombre   ?? 'N/A',
                'materia'         => $cm?->materia?->nombre   ?? 'N/A',
                'semestre'        => $cm?->materia?->semestre ?? 'N/A',
                'nota'            => $nota !== null ? number_format((float) $nota, 2) : null,
                'estadoAcademico' => $nota === null ? 'Sin nota' : ($nota >= 51 ? 'Aprobada' : 'Reprobada'),
            ];
        })->sortBy([['periodo', 'asc'], ['semestre', 'asc'], ['materia', 'asc']])->values();

        return [
            'cabecera' => [
                'nombre'  => $usuario->nombreCompleto,
                'ci'      => $usuario->ci,
                'correo'  => $usuario->correo,
                'carrera' => $ec?->carrera?->nombre ?? 'Sin carrera asignada',
            ],
            'historial' => $historial,
        ];
    }

    public function reporteKardex(Request $request): JsonResponse
    {
        $ci = trim($request->query('ci', ''));
        if (!$ci) {
            return response()->json(['message' => 'El campo CI es obligatorio.'], 422);
        }

        $result = $this->buildKardexData($ci);

        // Si buildKardexData devolvió una respuesta de error, la retornamos
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return response()->json($result);
    }

    public function exportarKardex(Request $request)
    {
        $ci = trim($request->query('ci', ''));
        if (!$ci) {
            return response()->json(['message' => 'El CI es obligatorio para exportar.'], 422);
        }

        $result = $this->buildKardexData($ci);
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        if ($request->query('formato') === 'excel') {
            $headings = ['Período', 'Materia', 'Semestre', 'Nota', 'Estado Académico'];
            $rows = collect($result['historial'])->map(fn ($r) => [
                $r['periodo'], $r['materia'], $r['semestre'], $r['nota'] ?? '—', $r['estadoAcademico'],
            ]);
            return Excel::download(new ReporteExport($rows, $headings), "kardex_{$ci}.xlsx");
        }

        $pdf = Pdf::loadView('reportes.kardex_pdf', [
            'cabecera'  => $result['cabecera'],
            'historial' => $result['historial'],
            'fecha'     => now()->format('d/m/Y H:i'),
        ]);
        return $pdf->download("kardex_{$ci}.pdf");
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 3: AUDITORÍA DE NOTAS
    // ──────────────────────────────────────────────────────────────────────────

    private function buildAuditoriaData(Request $request): array
    {
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');

        $query = Auditoria::with('usuarioResponsable')
            ->where('tabla_nombre', 'notas')
            ->orderBy('fecha_a', 'desc')
            ->limit(1000);

        if ($fechaDesde) $query->whereDate('fecha_a', '>=', $fechaDesde);
        if ($fechaHasta) $query->whereDate('fecha_a', '<=', $fechaHasta);

        $auditorias = $query->get();

        $headings = ['Fecha', 'Usuario Responsable', 'Acción', 'Campo', 'Valor Anterior', 'Valor Nuevo', 'IP'];
        $data = $auditorias->map(fn ($a) => [
            $a->fecha_a?->format('Y-m-d H:i:s') ?? '—',
            $a->usuarioResponsable?->nombreCompleto ?? 'Sistema',
            match ($a->accion) {
                'I' => 'Inserción',
                'U' => 'Actualización',
                'D' => 'Eliminación',
                default => $a->accion ?? '—',
            },
            $a->campo          ?? '—',
            $a->valor_anterior ?? '—',
            $a->valor_nuevo    ?? '—',
            $a->direccion_ip   ?? '—',
        ]);

        return ['headings' => $headings, 'data' => $data];
    }

    public function reporteAuditoriaNotas(Request $request): JsonResponse
    {
        $payload = $this->buildAuditoriaData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'No se encontraron registros de auditoría para el rango indicado.', 'headings' => $payload['headings'], 'data' => []], 200);
        }
        return response()->json(['headings' => $payload['headings'], 'data' => $payload['data']]);
    }

    public function exportarAuditoriaNotas(Request $request)
    {
        $payload = $this->buildAuditoriaData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'Sin datos para exportar.'], 404);
        }
        if ($request->query('formato') === 'excel') {
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'auditoria_notas.xlsx');
        }
        $pdf = Pdf::loadView('reportes.auditoria_pdf', ['headings' => $payload['headings'], 'data' => $payload['data']]);
        return $pdf->setPaper('a4', 'landscape')->download('auditoria_notas.pdf');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 4: OCUPACIÓN DE CURSOS
    // ──────────────────────────────────────────────────────────────────────────

    private function buildOcupacionData(Request $request): array
    {
        $idPeriodo = $request->query('idPeriodo');

        $query = CursoMateria::query()
            ->with(['curso', 'materia', 'docente', 'periodo'])
            ->withCount([
                'inscripciones as total_inscritos' => fn ($q) => $q->where('estado', 1),
            ])
            ->where('cursos_materias.estado', 1);

        if ($idPeriodo) {
            $query->where('cursos_materias.idPeriodo', $idPeriodo);
        }

        $resultados = $query->get();

        $headings = ['Curso', 'Materia', 'Semestre', 'Período', 'Docente', 'Capacidad', 'Inscritos', 'Disponibles', '% Ocupación'];
        $data = $resultados->map(function ($cm) {
            $capacidad   = (int) ($cm->curso?->capacidad ?? 0);
            $inscritos   = (int) $cm->total_inscritos;
            $disponibles = max(0, $capacidad - $inscritos);
            $pct         = $capacidad > 0 ? round(($inscritos / $capacidad) * 100, 1) : 0;

            return [
                $cm->idCurso,
                $cm->materia?->nombre         ?? 'N/A',
                $cm->materia?->semestre        ?? 'N/A',
                $cm->periodo?->nombre          ?? 'N/A',
                $cm->docente?->nombreCompleto  ?? 'Sin asignar',
                $capacidad,
                $inscritos,
                $disponibles,
                "{$pct}%",
            ];
        })
        ->sortByDesc(fn ($row) => (float) rtrim($row[8], '%'))
        ->values();

        return ['headings' => $headings, 'data' => $data];
    }

    public function reporteOcupacion(Request $request): JsonResponse
    {
        $payload = $this->buildOcupacionData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'Sin cursos registrados para el filtro aplicado.', 'headings' => $payload['headings'], 'data' => []], 200);
        }
        return response()->json(['headings' => $payload['headings'], 'data' => $payload['data']]);
    }

    public function exportarOcupacion(Request $request)
    {
        $payload = $this->buildOcupacionData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'Sin datos para exportar.'], 404);
        }
        if ($request->query('formato') === 'excel') {
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'ocupacion_cursos.xlsx');
        }
        $pdf = Pdf::loadView('reportes.ocupacion_pdf', ['headings' => $payload['headings'], 'data' => $payload['data']]);
        return $pdf->setPaper('a4', 'landscape')->download('ocupacion_cursos.pdf');
    }
}
