<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Horario de Docente</title>
<style>
  @page { margin: 2cm; }
  body { margin: 0; padding: 0; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #000; background: #fff; }

  /* ── Encabezado ── */
  .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 16px; }
  .header-inst { font-size: 14px; font-weight: 700; color: #000; letter-spacing: 0.01em; }
  .header-report { font-size: 11px; font-weight: 600; color: #000; margin-top: 3px; }
  .header-meta { font-size: 9px; color: #444; margin-top: 5px; }

  /* ── Ficha del Docente ── */
  .ficha {
    border: 1px solid #000;
    margin-bottom: 18px;
  }
  .ficha-title {
    background: #ebebeb; color: #000;
    padding: 5px 10px;
    font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    border-bottom: 1px solid #bbb;
  }
  .ficha-body { display: table; width: 100%; }
  .ficha-row  { display: table-row; }
  .ficha-cell {
    display: table-cell;
    padding: 6px 10px;
    border-right: 1px solid #ddd;
    width: 100%;
    vertical-align: top;
  }
  .ficha-cell:last-child { border-right: none; }
  .ficha-label { font-size: 8px; text-transform: uppercase; font-weight: 700; color: #555; letter-spacing: 0.05em; }
  .ficha-value { font-size: 11px; font-weight: 600; color: #000; margin-top: 2px; }

  /* ── Tabla ── */
  table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  thead th {
    background: #ebebeb; color: #000;
    padding: 7px 9px; text-align: left;
    font-size: 8.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.04em;
    border: 1px solid #bbb;
  }
  tbody td {
    padding: 6px 9px;
    border: 1px solid #ccc;
    color: #000;
    vertical-align: top;
    white-space: pre-wrap;
  }
  tbody tr:nth-child(even) { background: #f4f4f4; }

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
  <div class="header-report">Horario de Clases por Docente</div>
  <div class="header-meta">Generado el {{ $fecha }}</div>
</div>

<div class="ficha">
  <div class="ficha-title">Datos del Docente</div>
  <div class="ficha-body">
    <div class="ficha-row">
      <div class="ficha-cell">
        <div class="ficha-label">Nombre Completo</div>
        <div class="ficha-value">{{ $docente }}</div>
      </div>
    </div>
  </div>
</div>

<table>
  <thead>
    <tr>
      @foreach($headings as $h)
        <th>{{ $h }}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @forelse($data as $row)
    <tr>
      <td>{{ $row[0] }}</td>
      <td>{{ $row[1] }}</td>
      <td>{{ $row[2] }}</td>
      <td>{{ $row[3] }}</td>
    </tr>
    @empty
    <tr><td colspan="{{ count($headings) }}" style="text-align:center;padding:20px;color:#666;">No hay clases asignadas para este docente.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>Universidad — Documento Confidencial</span>
  <span>Pág. <span class="page-number"></span></span>
</div>
</body>
</html>
