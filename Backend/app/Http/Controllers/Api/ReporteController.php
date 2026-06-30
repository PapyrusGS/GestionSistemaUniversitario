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
        
        $docentes = User::where('estado', 1)
            ->whereHas('rol', fn ($q) => $q->where('nombre', 'Docente'))
            ->get()->map(function($d) {
                return ['idDocente' => $d->idUsuario, 'nombre' => trim("{$d->nombre1} {$d->apellido1} {$d->apellido2}")];
            });

        $usuarios = User::where('estado', 1)->get()->map(fn ($u) => [
            'idUsuario' => $u->idUsuario,
            'nombre'    => $u->nombreCompleto,
        ]);

        // Tablas auditables
        $tablasAuditables = [
            ['key' => '', 'label' => 'Todas las tablas'],
            ['key' => 'usuarios',          'label' => 'Usuarios'],
            ['key' => 'carreras',          'label' => 'Carreras'],
            ['key' => 'materias',          'label' => 'Materias'],
            ['key' => 'cursos',            'label' => 'Cursos'],
            ['key' => 'cursos_materias',   'label' => 'Cursos-Materias'],
            ['key' => 'notas',             'label' => 'Notas'],
            ['key' => 'estudiantemateria', 'label' => 'Inscripciones'],
            ['key' => 'horarios',          'label' => 'Horarios'],
            ['key' => 'periodos',          'label' => 'Períodos'],
            ['key' => 'roles',             'label' => 'Roles'],
        ];

        return response()->json([
            'cursos'            => $cursos,
            'materias'          => $materias,
            'carreras'          => $carreras,
            'periodos'          => $periodos,
            'docentes'          => $docentes,
            'usuarios'          => $usuarios,
            'tablasAuditables'  => $tablasAuditables,
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
        $idDocente = $request->query('idDocente');
        $semestre  = $request->query('semestre');

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
            ) AS promedio_nota, (
                SELECT COUNT(*)
                FROM estudiantemateria em2
                INNER JOIN notas n2 ON n2.idInscripcion = em2.idInscripcion
                WHERE em2.idCursoMateria = cursos_materias.idCursoMateria
                  AND em2.estado = 1
                  AND n2.estado  = 1
                  AND n2.nota >= 51
            ) AS total_aprobados')
            ->where('cursos_materias.estado', 1);

        if ($idPeriodo) $query->where('cursos_materias.idPeriodo', $idPeriodo);
        if ($idDocente) $query->where('cursos_materias.idDocente', $idDocente);
        if ($idCarrera) $query->whereHas('materia', fn ($q) => $q->where('idCarrera', $idCarrera));
        if ($semestre)  $query->whereHas('materia', fn ($q) => $q->where('semestre', (int) $semestre));

        $resultados = $query->orderBy('cursos_materias.idPeriodo')->get();

        $headings = ['Materia', 'Semestre', 'Período', 'Docente', 'Inscritos', 'Promedio', '% Aprobación'];
        $data = $resultados->map(function ($cm) {
            $inscritos  = (int) $cm->total_inscritos;
            $aprobados  = (int) ($cm->total_aprobados ?? 0);
            $pctAprob   = $inscritos > 0 ? round(($aprobados / $inscritos) * 100, 1) . '%' : 'N/A';
            return [
                $cm->materia?->nombre         ?? 'N/A',
                $cm->materia?->semestre        ?? 'N/A',
                $cm->periodo?->nombre          ?? 'N/A',
                $cm->docente?->nombreCompleto  ?? 'Sin asignar',
                $inscritos,
                $cm->promedio_nota !== null ? number_format((float) $cm->promedio_nota, 2) : 'Sin notas',
                $pctAprob,
            ];
        });

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
        return $pdf->setPaper('letter', 'portrait')->download('rendimiento.pdf');
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
        return $pdf->setPaper('letter', 'portrait')->download("kardex_{$ci}.pdf");
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 3: AUDITORÍA GENERAL
    // ──────────────────────────────────────────────────────────────────────────

    private function buildAuditoriaData(Request $request): array
    {
        $fechaDesde  = $request->query('fecha_desde');
        $fechaHasta  = $request->query('fecha_hasta');
        $tabla       = $request->query('tabla');
        $accion      = $request->query('accion');
        $idUsuario   = $request->query('idUsuario');

        $query = Auditoria::with('usuarioResponsable')
            ->orderBy('fecha_a', 'desc')
            ->limit(2000);

        if ($fechaDesde) $query->whereDate('fecha_a', '>=', $fechaDesde);
        if ($fechaHasta) $query->whereDate('fecha_a', '<=', $fechaHasta);
        if ($tabla)      $query->where('tabla_nombre', $tabla);
        if ($accion)     $query->where('accion', $accion);
        if ($idUsuario)  $query->where('usuario_a', $idUsuario);

        $auditorias = $query->get();

        $headings = ['Fecha', 'Usuario', 'Tabla', 'Acción', 'Campo', 'Valor Anterior', 'Valor Nuevo'];
        $data = $auditorias->map(fn ($a) => [
            $a->fecha_a?->format('Y-m-d H:i:s') ?? '—',
            $a->usuarioResponsable?->nombreCompleto ?? 'Sistema',
            $a->tabla_nombre   ?? '—',
            match ($a->accion) {
                'C' => 'Creación',
                'U' => 'Actualización',
                'D' => 'Eliminación',
                default => $a->accion ?? '—',
            },
            $a->campo          ?? '—',
            $a->valor_anterior ?? '—',
            $a->valor_nuevo    ?? '—',
        ]);

        return ['headings' => $headings, 'data' => $data];
    }

    public function reporteAuditoriaNotas(Request $request): JsonResponse
    {
        $payload = $this->buildAuditoriaData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'No se encontraron registros de auditoría para los filtros indicados.', 'headings' => $payload['headings'], 'data' => []], 200);
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
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'auditoria.xlsx');
        }
        $pdf = Pdf::loadView('reportes.auditoria_pdf', ['headings' => $payload['headings'], 'data' => $payload['data']]);
        return $pdf->setPaper('letter', 'portrait')->download('auditoria.pdf');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 4: OCUPACIÓN DE CURSOS
    // ──────────────────────────────────────────────────────────────────────────

    private function buildOcupacionData(Request $request): array
    {
        $idPeriodo = $request->query('idPeriodo');
        $idCarrera = $request->query('idCarrera');
        $idDocente = $request->query('idDocente');

        $query = CursoMateria::query()
            ->with(['curso', 'materia', 'docente', 'periodo'])
            ->withCount([
                'inscripciones as total_inscritos' => fn ($q) => $q->where('estado', 1),
            ])
            ->where('cursos_materias.estado', 1);

        if ($idPeriodo) $query->where('cursos_materias.idPeriodo', $idPeriodo);
        if ($idCarrera) $query->whereHas('materia', fn ($q) => $q->where('idCarrera', $idCarrera));
        if ($idDocente) $query->where('cursos_materias.idDocente', $idDocente);

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
        return $pdf->setPaper('letter', 'portrait')->download('ocupacion_cursos.pdf');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 5: HORARIO DE DOCENTE
    // ──────────────────────────────────────────────────────────────────────────

    private function buildHorarioDocenteData(Request $request): array
    {
        $idDocente = $request->query('idDocente');
        $idPeriodo = $request->query('idPeriodo');

        if (!$idDocente) {
            return ['error' => 'Debe seleccionar un docente.'];
        }

        $docente = User::where('idUsuario', $idDocente)
            ->whereHas('rol', fn ($q) => $q->where('nombre', 'Docente'))
            ->first();

        if (!$docente) {
            return ['error' => 'Docente no encontrado.'];
        }

        $query = CursoMateria::with(['materia', 'horarios', 'periodo'])
            ->where('idDocente', $idDocente)
            ->where('cursos_materias.estado', 1);

        if ($idPeriodo) {
            $query->where('cursos_materias.idPeriodo', $idPeriodo);
        }

        $cursosMaterias = $query->get();

        $diasSemana = [
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
        ];

        $data = [];
        foreach ($cursosMaterias as $cm) {
            $horariosFormateados = [];
            foreach (($cm->horarios ?? []) as $h) {
                $dia = $diasSemana[$h->diaSemana] ?? $h->diaSemana;
                $inicio = substr($h->horaInicio, 0, 5);
                $fin = substr($h->horaFin, 0, 5);
                $horariosFormateados[] = "{$dia} {$inicio}-{$fin}";
            }

            $data[] = [
                $cm->periodo ? $cm->periodo->nombre : 'Sin periodo',
                $cm->materia ? $cm->materia->nombre : 'N/A',
                $cm->curso ? $cm->curso->idCurso : 'N/A',
                empty($horariosFormateados) ? 'Sin horario asignado' : implode("\n", $horariosFormateados)
            ];
        }

        $headings = ['Periodo', 'Materia', 'Curso', 'Horarios Asignados'];
        return ['headings' => $headings, 'data' => collect($data), 'docente' => $docente];
    }

    public function reporteHorarioDocente(Request $request): JsonResponse
    {
        $payload = $this->buildHorarioDocenteData($request);
        
        if (isset($payload['error'])) {
            return response()->json(['message' => $payload['error']], 400);
        }

        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'El docente no tiene clases asignadas en este periodo.', 'headings' => $payload['headings'], 'data' => []], 200);
        }

        return response()->json([
            'docente' => $payload['docente']->nombreCompleto,
            'headings' => $payload['headings'],
            'data' => $payload['data']
        ]);
    }

    public function exportarHorarioDocente(Request $request)
    {
        $payload = $this->buildHorarioDocenteData($request);

        if (isset($payload['error'])) {
            return response()->json(['message' => $payload['error']], 400);
        }

        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'Sin datos para exportar.'], 404);
        }

        if ($request->query('formato') === 'excel') {
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'horario_docente.xlsx');
        }

        $pdf = Pdf::loadView('reportes.horario_docente_pdf', [
            'docente' => $payload['docente']->nombreCompleto,
            'headings' => $payload['headings'],
            'data' => $payload['data'],
            'fecha' => now()->format('d/m/Y H:i')
        ]);
        return $pdf->setPaper('letter', 'portrait')->download('horario_docente.pdf');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 6: ESTUDIANTES POR CARRERA
    // ──────────────────────────────────────────────────────────────────────────

    private function buildEstudiantesCarreraData(Request $request): array
    {
        $idCarrera = $request->query('idCarrera');

        $query = EstudianteCarrera::with(['estudiante.usuario', 'carrera'])
            ->where('estado', 1);

        if ($idCarrera) {
            $query->where('idCarrera', $idCarrera);
        }

        $registros = $query->get();

        $headings = ['Carrera', 'Nombre Completo', 'C.I.', 'Correo', 'Teléfono'];
        $data = $registros->map(function ($ec) {
            $usuario = $ec->estudiante?->usuario;
            return [
                $ec->carrera?->nombre          ?? 'N/A',
                $usuario?->nombreCompleto       ?? 'N/A',
                $usuario?->ci                  ?? 'N/A',
                $usuario?->correo              ?? 'N/A',
                $usuario?->telefono            ?? '—',
            ];
        })->sortBy([['carrera', 'asc'], ['nombre', 'asc']])->values();

        return ['headings' => $headings, 'data' => $data];
    }

    public function reporteEstudiantesCarrera(Request $request): JsonResponse
    {
        $payload = $this->buildEstudiantesCarreraData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'No hay estudiantes inscritos para el filtro aplicado.', 'headings' => $payload['headings'], 'data' => []], 200);
        }
        return response()->json(['headings' => $payload['headings'], 'data' => $payload['data']]);
    }

    public function exportarEstudiantesCarrera(Request $request)
    {
        $payload = $this->buildEstudiantesCarreraData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'Sin datos para exportar.'], 404);
        }
        if ($request->query('formato') === 'excel') {
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'estudiantes_carrera.xlsx');
        }
        $pdf = Pdf::loadView('reportes.estudiantes_carrera_pdf', ['headings' => $payload['headings'], 'data' => $payload['data']]);
        return $pdf->setPaper('letter', 'portrait')->download('estudiantes_carrera.pdf');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REPORTE 7: CARGA DOCENTE
    // ──────────────────────────────────────────────────────────────────────────

    private function buildCargaDocenteData(Request $request): array
    {
        $idPeriodo = $request->query('idPeriodo');

        $docentes = User::where('estado', 1)
            ->whereHas('rol', fn ($q) => $q->where('nombre', 'Docente'))
            ->with(['cursosMaterias' => function ($q) use ($idPeriodo) {
                $q->where('cursos_materias.estado', 1);
                if ($idPeriodo) $q->where('cursos_materias.idPeriodo', $idPeriodo);
                $q->withCount([
                    'inscripciones as total_inscritos' => fn ($qi) => $qi->where('estado', 1),
                ]);
            }, 'cursosMaterias.materia', 'cursosMaterias.periodo'])
            ->get();

        $headings = ['Docente', 'C.I.', 'Materias asignadas', 'Total Estudiantes', 'Período(s)'];
        $data = $docentes->map(function ($docente) {
            $cms = $docente->cursosMaterias;
            $materias  = $cms->pluck('materia.nombre')->filter()->unique()->implode(', ') ?: '—';
            $periodos  = $cms->pluck('periodo.nombre')->filter()->unique()->implode(', ') ?: '—';
            $totalEst  = $cms->sum('total_inscritos');
            return [
                $docente->nombreCompleto,
                $docente->ci,
                $cms->count(),
                $totalEst,
                $periodos,
            ];
        })->sortByDesc(fn ($r) => $r[2])->values();

        return ['headings' => $headings, 'data' => $data];
    }

    public function reporteCargaDocente(Request $request): JsonResponse
    {
        $payload = $this->buildCargaDocenteData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'No hay docentes registrados.', 'headings' => $payload['headings'], 'data' => []], 200);
        }
        return response()->json(['headings' => $payload['headings'], 'data' => $payload['data']]);
    }

    public function exportarCargaDocente(Request $request)
    {
        $payload = $this->buildCargaDocenteData($request);
        if ($payload['data']->isEmpty()) {
            return response()->json(['message' => 'Sin datos para exportar.'], 404);
        }
        if ($request->query('formato') === 'excel') {
            return Excel::download(new ReporteExport($payload['data'], $payload['headings']), 'carga_docente.xlsx');
        }
        $pdf = Pdf::loadView('reportes.carga_docente_pdf', ['headings' => $payload['headings'], 'data' => $payload['data']]);
        return $pdf->setPaper('letter', 'portrait')->download('carga_docente.pdf');
    }
}
