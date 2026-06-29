<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Kardex Academico</title>
<style>
  @page { margin: 2cm; }
  body { margin: 0; padding: 0; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #000; background: #fff; }

  /* ── Encabezado ── */
  .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 16px; }
  .header-inst { font-size: 14px; font-weight: 700; color: #000; letter-spacing: 0.01em; }
  .header-report { font-size: 11px; font-weight: 600; color: #000; margin-top: 3px; }
  .header-meta { font-size: 9px; color: #444; margin-top: 5px; }

  /* ── Ficha del estudiante ── */
  .ficha {
    border: 1px solid #000;
    margin-bottom: 18px;
  }
  .ficha-title {
    background: #000; color: #fff;
    padding: 5px 10px;
    font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
  }
  .ficha-body { display: table; width: 100%; }
  .ficha-row  { display: table-row; }
  .ficha-cell {
    display: table-cell;
    padding: 6px 10px;
    border-right: 1px solid #ddd;
    width: 25%;
    vertical-align: top;
  }
  .ficha-cell:last-child { border-right: none; }
  .ficha-label { font-size: 8px; text-transform: uppercase; font-weight: 700; color: #555; letter-spacing: 0.05em; }
  .ficha-value { font-size: 11px; font-weight: 600; color: #000; margin-top: 2px; }

  /* ── Tabla de historial ── */
  table { width: 100%; border-collapse: collapse; margin-top: 0; }
  thead th {
    background: #ebebeb; color: #000;
    padding: 7px 9px; text-align: left;
    font-size: 8.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.04em;
    border: 1px solid #bbb;
  }
  tbody td { padding: 6px 9px; border: 1px solid #ccc; color: #000; }
  tbody tr:nth-child(even) { background: #f4f4f4; }
  .nota-cell { text-align: right; font-weight: 600; }
  .estado-aprobada { font-weight: 700; color: #000; }
  .estado-reprobada { font-weight: 700; color: #000; text-decoration: underline; }
  .estado-sin { color: #666; font-style: italic; }

  /* ── Pie ── */
  .footer {
    position: fixed; bottom: 0; left: 0; right: 0;
    border-top: 1px solid #000;
    padding: 5px 0;
    font-size: 8px; color: #333;
    display: flex; justify-content: space-between;
  }
  .page-number:after { content: counter(page); }
</style>
</head>
<body>
<div class="header">
  <div class="header-inst">Sistema de Gestión Universitaria</div>
  <div class="header-report">Kardex Académico del Estudiante</div>
  <div class="header-meta">Generado el {{ $fecha }}</div>
</div>

<div class="ficha">
  <div class="ficha-title">Datos del Estudiante</div>
  <div class="ficha-body">
    @php
      $conNota = collect($historial)->filter(fn($r) => !is_null($r['nota']) && $r['nota'] !== '' && $r['nota'] !== '—');
      $aprobadas = $conNota->filter(fn($r) => floatval($r['nota']) >= 51)->count();
      $promedio = $conNota->count() ? round($conNota->avg('nota'), 1) : '—';
      $total = count($historial);
    @endphp
    <div class="ficha-row">
      <div class="ficha-cell" style="width: 35%;">
        <div class="ficha-label">Nombre Completo</div>
        <div class="ficha-value">{{ $cabecera['nombre'] }}</div>
      </div>
      <div class="ficha-cell" style="width: 15%;">
        <div class="ficha-label">C.I.</div>
        <div class="ficha-value">{{ $cabecera['ci'] }}</div>
      </div>
      <div class="ficha-cell" style="width: 25%;">
        <div class="ficha-label">Correo Electrónico</div>
        <div class="ficha-value">{{ $cabecera['correo'] }}</div>
      </div>
      <div class="ficha-cell" style="width: 25%;">
        <div class="ficha-label">Carrera</div>
        <div class="ficha-value">{{ $cabecera['carrera'] }}</div>
      </div>
    </div>
    <div class="ficha-row" style="border-top: 1px solid #ddd;">
      <div class="ficha-cell" style="width: 35%; background: #fafafa;">
        <div class="ficha-label">Resumen Académico</div>
        <div class="ficha-value" style="font-size: 10px; color: #555;">Estadísticas del historial</div>
      </div>
      <div class="ficha-cell" style="width: 15%; background: #fafafa;">
        <div class="ficha-label">Materias Reg.</div>
        <div class="ficha-value">{{ $total }}</div>
      </div>
      <div class="ficha-cell" style="width: 25%; background: #fafafa;">
        <div class="ficha-label">Aprobadas / Reprobadas</div>
        <div class="ficha-value">{{ $aprobadas }} / {{ $total - $aprobadas }}</div>
      </div>
      <div class="ficha-cell" style="width: 25%; background: #fafafa;">
        <div class="ficha-label">Promedio de Notas</div>
        <div class="ficha-value">{{ $promedio }}</div>
      </div>
    </div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>Período</th>
      <th>Materia</th>
      <th style="width:50px;text-align:center;">Sem.</th>
      <th style="width:60px;text-align:right;">Nota Final</th>
      <th style="width:100px;">Estado Académico</th>
    </tr>
  </thead>
  <tbody>
    @forelse($historial as $fila)
    <tr>
      <td>{{ $fila['periodo'] }}</td>
      <td>{{ $fila['materia'] }}</td>
      <td style="text-align:center;">{{ $fila['semestre'] }}</td>
      <td class="nota-cell">{{ $fila['nota'] ?? '—' }}</td>
      <td>
        @if($fila['estadoAcademico'] === 'Aprobada')
          <span class="estado-aprobada">Aprobada</span>
        @elseif($fila['estadoAcademico'] === 'Reprobada')
          <span class="estado-reprobada">Reprobada</span>
        @else
          <span class="estado-sin">Sin nota</span>
        @endif
      </td>
    </tr>
    @empty
    <tr><td colspan="5" style="text-align:center;padding:20px;color:#666;">Sin historial académico registrado.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>Universidad — Documento Confidencial</span>
  <span>Pág. <span class="page-number"></span></span>
</div>
</body>
</html>
