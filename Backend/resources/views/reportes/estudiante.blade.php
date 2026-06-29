<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte Académico — {{ $usuario }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'DejaVu Sans', Arial, sans-serif;
      font-size: 11px;
      color: #1a1a1a;
      background: #fff;
      padding: 36px 40px;
    }

    /* ══════════════════════════════════════
       ENCABEZADO INSTITUCIONAL
    ══════════════════════════════════════ */
    .header {
      margin-bottom: 28px;
    }

    .header-band {
      background: #4a6741;
      border-radius: 10px 10px 0 0;
      padding: 18px 24px 14px;
      color: #fff;
    }

    .header-band .inst {
      font-size: 9px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      opacity: 0.75;
      margin-bottom: 4px;
    }

    .header-band .title {
      font-size: 18px;
      font-weight: 700;
      letter-spacing: -0.01em;
    }

    .header-band .subtitle {
      font-size: 10px;
      opacity: 0.7;
      margin-top: 3px;
    }

    .header-meta {
      background: #f0f4ef;
      border: 1px solid #d4e0d1;
      border-top: none;
      border-radius: 0 0 10px 10px;
      padding: 14px 24px;
      display: table;
      width: 100%;
    }

    .meta-row {
      display: table-row;
    }

    .meta-cell {
      display: table-cell;
      width: 25%;
      padding: 4px 12px 4px 0;
    }

    .meta-cell .m-label {
      font-size: 8.5px;
      font-weight: 700;
      color: #4a6741;
      text-transform: uppercase;
      letter-spacing: 0.07em;
      display: block;
      margin-bottom: 2px;
    }

    .meta-cell .m-value {
      font-size: 10.5px;
      font-weight: 600;
      color: #1a1a1a;
    }

    /* ══════════════════════════════════════
       TARJETAS DE RESUMEN
    ══════════════════════════════════════ */
    .summary-section {
      margin-bottom: 22px;
    }

    .section-title {
      font-size: 9px;
      font-weight: 700;
      color: #4a6741;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-bottom: 10px;
      padding-bottom: 5px;
      border-bottom: 2px solid #d4e0d1;
    }

    .cards-row {
      display: table;
      width: 100%;
      border-spacing: 8px 0;
    }

    .card {
      display: table-cell;
      background: #f9faf8;
      border: 1px solid #d4e0d1;
      border-radius: 8px;
      padding: 12px 16px;
      text-align: center;
      border-top: 3px solid #4a6741;
    }

    .card.green { border-top-color: #3d8b5e; background: #f0faf4; border-color: #b8dfc8; }
    .card.red   { border-top-color: #c0392b; background: #fdf2f2; border-color: #f0c4c0; }
    .card.amber { border-top-color: #c07a1a; background: #fdf8f0; border-color: #f0deb8; }
    .card.blue  { border-top-color: #2471a3; background: #f0f6fd; border-color: #b8d4f0; }

    .card .c-val {
      font-size: 22px;
      font-weight: 700;
      color: #1a1a1a;
      display: block;
      line-height: 1.1;
    }

    .card .c-label {
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #5b5c5e;
      display: block;
      margin-top: 4px;
    }

    /* ══════════════════════════════════════
       TABLAS GENERALES
    ══════════════════════════════════════ */
    .data-section {
      margin-bottom: 24px;
    }

    table.main-table {
      width: 100%;
      border-collapse: collapse;
    }

    table.main-table thead tr {
      background: #4a6741;
    }

    table.main-table thead th {
      color: #fff;
      font-size: 8.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      padding: 9px 12px;
      text-align: left;
    }

    table.main-table tbody tr:nth-child(even) { background: #f6f8f5; }
    table.main-table tbody tr:nth-child(odd)  { background: #fff; }

    table.main-table tbody td {
      padding: 9px 12px;
      font-size: 10.5px;
      color: #333;
      border-bottom: 1px solid #eaeee8;
      vertical-align: middle;
    }

    /* ══════════════════════════════════════
       BADGES DE ESTADO
    ══════════════════════════════════════ */
    .badge {
      display: inline-block;
      padding: 3px 9px;
      border-radius: 20px;
      font-size: 8.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .badge-aprobado  { background: #d4f0e0; color: #1e6e45; }
    .badge-reprobado { background: #fde0dc; color: #922b21; }
    .badge-pendiente { background: #fef3cd; color: #7d5a00; }
    .badge-inscrito  { background: #dce8f9; color: #1a4f8a; }

    /* nota resaltada */
    .nota-alta  { color: #1e6e45; font-weight: 700; }
    .nota-media { color: #7d5a00; font-weight: 700; }
    .nota-baja  { color: #922b21; font-weight: 700; }

    /* ══════════════════════════════════════
       BLOQUE HORARIO (inscripciones)
    ══════════════════════════════════════ */
    .horario-chip {
      background: #eef2fd;
      border: 1px solid #c5d3f5;
      border-radius: 6px;
      padding: 2px 7px;
      font-size: 9.5px;
      color: #1a3a7a;
      font-weight: 600;
    }

    /* ══════════════════════════════════════
       HISTORIAL — agrupado por periodo
    ══════════════════════════════════════ */
    .periodo-block {
      margin-bottom: 18px;
    }

    .periodo-header {
      background: #697d7b;
      color: #fff;
      padding: 7px 14px;
      border-radius: 6px 6px 0 0;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.04em;
    }

    .periodo-table {
      border: 1px solid #d0d8d6;
      border-top: none;
      border-radius: 0 0 6px 6px;
      overflow: hidden;
    }

    table.hist-table {
      width: 100%;
      border-collapse: collapse;
    }

    table.hist-table thead th {
      background: #f0f4f3;
      color: #697d7b;
      font-size: 8.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      padding: 7px 12px;
      text-align: left;
      border-bottom: 1px solid #d0d8d6;
    }

    table.hist-table tbody td {
      padding: 8px 12px;
      font-size: 10.5px;
      color: #333;
      border-bottom: 1px solid #eaeeec;
    }

    table.hist-table tbody tr:last-child td {
      border-bottom: none;
    }

    /* ══════════════════════════════════════
       PIE DE PÁGINA
    ══════════════════════════════════════ */
    .footer {
      margin-top: 30px;
      padding-top: 10px;
      border-top: 1px solid #d4e0d1;
    }

    .footer-inner {
      display: table;
      width: 100%;
    }

    .footer-left, .footer-right {
      display: table-cell;
      font-size: 8.5px;
      color: #8c9f96;
      vertical-align: bottom;
    }

    .footer-right { text-align: right; }

    .footer-note {
      margin-top: 6px;
      font-size: 8px;
      color: #aab8b4;
      font-style: italic;
    }
  </style>
</head>
<body>

{{-- ══════════════════════════════════════════════
     ENCABEZADO
══════════════════════════════════════════════ --}}
<div class="header">
  <div class="header-band">
    <p class="inst">Sistema de Gestión Académica</p>
    <p class="title">
      @php
        $titulos = [
          'inscripciones' => 'Reporte de Materias Inscritas',
          'notas'         => 'Reporte de Calificaciones',
          'historial'     => 'Historial Académico',
        ];
        echo $titulos[$tipo] ?? 'Reporte Académico';
      @endphp
    </p>
    <p class="subtitle">Documento de uso académico personal — generado automáticamente</p>
  </div>

  <div class="header-meta">
    <div class="meta-row">
      <div class="meta-cell">
        <span class="m-label">Estudiante</span>
        <span class="m-value">{{ $usuario }}</span>
      </div>
      <div class="meta-cell">
        <span class="m-label">Periodo</span>
        <span class="m-value">{{ $periodo === 'todos' ? 'Todos los periodos' : $periodo }}</span>
      </div>
      <div class="meta-cell">
        <span class="m-label">Tipo de reporte</span>
        <span class="m-value">{{ $titulos[$tipo] ?? ucfirst($tipo) }}</span>
      </div>
      <div class="meta-cell">
        <span class="m-label">Generado el</span>
        <span class="m-value">{{ now()->format('d/m/Y — H:i') }}</span>
      </div>
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════
     TIPO: INSCRIPCIONES
══════════════════════════════════════════════ --}}
@if($tipo === 'inscripciones')

  {{-- Resumen --}}
  <div class="summary-section">
    <p class="section-title">Resumen de inscripciones</p>
    <div class="cards-row">
      <div class="card blue">
        <span class="c-val">{{ count($datos) }}</span>
        <span class="c-label">Materias inscritas</span>
      </div>
      <div class="card">
        @php
          $turnos = collect($datos)->pluck('turno')->filter()->unique()->count();
        @endphp
        <span class="c-val">{{ $turnos }}</span>
        <span class="c-label">Turnos distintos</span>
      </div>
      <div class="card">
        @php
          $docentes = collect($datos)->pluck('docente')->filter()->unique()->count();
        @endphp
        <span class="c-val">{{ $docentes }}</span>
        <span class="c-label">Docentes asignados</span>
      </div>
      <div class="card">
        @php
          $periodos = collect($datos)->pluck('periodo')->filter()->unique()->count();
        @endphp
        <span class="c-val">{{ $periodos ?: '1' }}</span>
        <span class="c-label">Periodo(s)</span>
      </div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="data-section">
    <p class="section-title">Detalle de materias inscritas</p>
    <table class="main-table">
      <thead>
        <tr>
          <th>Materia</th>
          <th>Docente</th>
          <th>Turno</th>
          <th>Horario</th>
          <th>Periodo</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($datos as $fila)
          @php $fila = (array) $fila; @endphp
          <tr>
            <td><strong>{{ $fila['materia'] ?? $fila['materia_nombre'] ?? 'Sin dato' }}</strong></td>
            <td>{{ $fila['docente'] ?? $fila['docente_nombre'] ?? 'Sin asignar' }}</td>
            <td>{{ $fila['turno'] ?? $fila['turno_nombre'] ?? '—' }}</td>
            <td>
              @if(!empty($fila['horario']))
                <span class="horario-chip">{{ $fila['horario'] }}</span>
              @else
                <span style="color:#aaa;">—</span>
              @endif
            </td>
            <td>{{ $fila['periodo'] ?? $fila['periodo_nombre'] ?? $periodo }}</td>
            <td>
              <span class="badge badge-inscrito">Inscrito</span>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center; color:#aaa; padding:20px;">Sin registros de inscripción.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>


{{-- ══════════════════════════════════════════════
     TIPO: NOTAS
══════════════════════════════════════════════ --}}
@elseif($tipo === 'notas')

  @php
    $col         = collect($datos);
    $conNota     = $col->filter(fn($r) => isset(((array)$r)['nota']) && ((array)$r)['nota'] !== null);
    $aprobados   = $conNota->filter(fn($r) => ((array)$r)['nota'] >= 51)->count();
    $reprobados  = $conNota->filter(fn($r) => ((array)$r)['nota'] <  51)->count();
    $promedio    = $conNota->isNotEmpty()
                    ? number_format($conNota->avg(fn($r) => ((array)$r)['nota']), 2)
                    : '—';
    $mejor       = $conNota->isNotEmpty()
                    ? number_format($conNota->max(fn($r) => ((array)$r)['nota']), 1)
                    : '—';
  @endphp

  {{-- Resumen --}}
  <div class="summary-section">
    <p class="section-title">Resumen de calificaciones</p>
    <div class="cards-row">
      <div class="card blue">
        <span class="c-val">{{ count($datos) }}</span>
        <span class="c-label">Materias</span>
      </div>
      <div class="card green">
        <span class="c-val">{{ $aprobados }}</span>
        <span class="c-label">Aprobadas</span>
      </div>
      <div class="card red">
        <span class="c-val">{{ $reprobados }}</span>
        <span class="c-label">Reprobadas</span>
      </div>
      <div class="card amber">
        <span class="c-val">{{ $promedio }}</span>
        <span class="c-label">Promedio general</span>
      </div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="data-section">
    <p class="section-title">Detalle de calificaciones</p>
    <table class="main-table">
      <thead>
        <tr>
          <th>Materia</th>
          <th>Docente</th>
          <th>Turno / Horario</th>
          <th>Periodo</th>
          <th style="text-align:center;">Nota</th>
          <th style="text-align:center;">Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($datos as $fila)
          @php
            $fila  = (array) $fila;
            $nota  = $fila['nota'] ?? null;
            $aprob = $nota !== null && $nota >= 51;
            $notaClass = $nota === null ? '' : ($nota >= 71 ? 'nota-alta' : ($nota >= 51 ? 'nota-media' : 'nota-baja'));
          @endphp
          <tr>
            <td><strong>{{ $fila['materia'] ?? $fila['materia_nombre'] ?? 'Sin dato' }}</strong></td>
            <td>{{ $fila['docente'] ?? $fila['docente_nombre'] ?? 'Sin asignar' }}</td>
            <td>
              {{ $fila['turno'] ?? $fila['turno_nombre'] ?? '' }}
              @if(!empty($fila['horario']))
                <br><span class="horario-chip">{{ $fila['horario'] }}</span>
              @endif
            </td>
            <td>{{ $fila['periodo'] ?? $fila['periodo_nombre'] ?? $periodo }}</td>
            <td style="text-align:center;">
              @if($nota !== null)
                <span class="{{ $notaClass }}">{{ number_format((float)$nota, 1) }}</span>
              @else
                <span style="color:#aaa;">Sin nota</span>
              @endif
            </td>
            <td style="text-align:center;">
              @if($nota === null)
                <span class="badge badge-pendiente">Pendiente</span>
              @elseif($aprob)
                <span class="badge badge-aprobado">Aprobado</span>
              @else
                <span class="badge badge-reprobado">Reprobado</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center; color:#aaa; padding:20px;">Sin calificaciones registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>


{{-- ══════════════════════════════════════════════
     TIPO: HISTORIAL — agrupado por periodo
══════════════════════════════════════════════ --}}
@elseif($tipo === 'historial')

  @php
    $col        = collect($datos);
    $conNota    = $col->filter(fn($r) => isset(((array)$r)['nota']) && ((array)$r)['nota'] !== null);
    $aprobados  = $conNota->filter(fn($r) => ((array)$r)['nota'] >= 51)->count();
    $reprobados = $conNota->filter(fn($r) => ((array)$r)['nota'] <  51)->count();
    $promedio   = $conNota->isNotEmpty()
                    ? number_format($conNota->avg(fn($r) => ((array)$r)['nota']), 2)
                    : '—';
    // Agrupar por periodo
    $porPeriodo = $col->groupBy(fn($r) => ((array)$r)['periodo'] ?? ((array)$r)['periodo_nombre'] ?? 'Sin periodo');
  @endphp

  {{-- Resumen global --}}
  <div class="summary-section">
    <p class="section-title">Resumen histórico</p>
    <div class="cards-row">
      <div class="card blue">
        <span class="c-val">{{ count($datos) }}</span>
        <span class="c-label">Total materias</span>
      </div>
      <div class="card green">
        <span class="c-val">{{ $aprobados }}</span>
        <span class="c-label">Aprobadas</span>
      </div>
      <div class="card red">
        <span class="c-val">{{ $reprobados }}</span>
        <span class="c-label">Reprobadas</span>
      </div>
      <div class="card amber">
        <span class="c-val">{{ $promedio }}</span>
        <span class="c-label">Promedio histórico</span>
      </div>
    </div>
  </div>

  {{-- Bloques por periodo --}}
  <div class="data-section">
    <p class="section-title">Detalle por periodo académico</p>

    @foreach($porPeriodo as $nombrePeriodo => $filasPeriodo)
      @php
        $filasArr   = $filasPeriodo->map(fn($r) => (array) $r);
        $aprobP     = $filasArr->filter(fn($r) => isset($r['nota']) && $r['nota'] >= 51)->count();
        $reprobP    = $filasArr->filter(fn($r) => isset($r['nota']) && $r['nota'] !== null && $r['nota'] < 51)->count();
        $promedioP  = $filasArr->filter(fn($r) => isset($r['nota']) && $r['nota'] !== null)
                               ->avg(fn($r) => $r['nota']);
      @endphp

      <div class="periodo-block">
        <div class="periodo-header">
          📅 {{ $nombrePeriodo }}
          &nbsp;·&nbsp; {{ $filasArr->count() }} materia(s)
          &nbsp;·&nbsp; {{ $aprobP }} aprobada(s)
          &nbsp;·&nbsp; {{ $reprobP }} reprobada(s)
          @if($promedioP) &nbsp;·&nbsp; Promedio: {{ number_format($promedioP, 2) }} @endif
        </div>
        <div class="periodo-table">
          <table class="hist-table">
            <thead>
              <tr>
                <th>Materia</th>
                <th>Docente</th>
                <th>Turno / Horario</th>
                <th style="text-align:center;">Nota</th>
                <th style="text-align:center;">Estado</th>
              </tr>
            </thead>
            <tbody>
              @foreach($filasArr as $fila)
                @php
                  $nota  = $fila['nota'] ?? null;
                  $aprob = $nota !== null && $nota >= 51;
                  $notaClass = $nota === null ? '' : ($nota >= 71 ? 'nota-alta' : ($nota >= 51 ? 'nota-media' : 'nota-baja'));
                @endphp
                <tr>
                  <td><strong>{{ $fila['materia'] ?? $fila['materia_nombre'] ?? '—' }}</strong></td>
                  <td>{{ $fila['docente'] ?? $fila['docente_nombre'] ?? 'Sin asignar' }}</td>
                  <td>
                    {{ $fila['turno'] ?? $fila['turno_nombre'] ?? '' }}
                    @if(!empty($fila['horario']))
                      — <span class="horario-chip">{{ $fila['horario'] }}</span>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    @if($nota !== null)
                      <span class="{{ $notaClass }}">{{ number_format((float)$nota, 1) }}</span>
                    @else
                      <span style="color:#aaa;">—</span>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    @if($nota === null)
                      <span class="badge badge-pendiente">Pendiente</span>
                    @elseif($aprob)
                      <span class="badge badge-aprobado">Aprobado</span>
                    @else
                      <span class="badge badge-reprobado">Reprobado</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  </div>

@endif


{{-- ══════════════════════════════════════════════
     PIE DE PÁGINA
══════════════════════════════════════════════ --}}
<div class="footer">
  <div class="footer-inner">
    <div class="footer-left">
      <strong>{{ $usuario }}</strong> — Reporte personal de uso académico<br>
      Sistema de Gestión Académica · {{ now()->format('d/m/Y H:i') }}
    </div>
    <div class="footer-right">
      Documento generado automáticamente<br>
      No requiere firma ni sello
    </div>
  </div>
  <p class="footer-note">
    La información contenida en este documento proviene del sistema oficial de registro académico.
    Cualquier discrepancia debe ser reportada a la secretaría académica.
  </p>
</div>

</body>
</html>