<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Auditoria de Cambios en Notas</title>
<style>
  @page { margin: 2cm; }
  body { margin: 0; padding: 0; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9.5px; color: #000; background: #fff; }

  /* ── Encabezado ── */
  .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 16px; }
  .header-inst { font-size: 14px; font-weight: 700; color: #000; letter-spacing: 0.01em; }
  .header-report { font-size: 11px; font-weight: 600; color: #000; margin-top: 3px; }
  .header-meta { font-size: 9px; color: #444; margin-top: 5px; }

  /* ── Tabla ── */
  table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  thead th {
    background: #ebebeb; color: #000;
    padding: 7px 8px; text-align: left;
    font-size: 8px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.04em;
    border: 1px solid #bbb;
  }
  tbody td {
    padding: 5px 8px;
    border: 1px solid #ccc;
    color: #000;
    vertical-align: top;
    word-break: break-word;
  }
  tbody tr:nth-child(even) { background: #f4f4f4; }
  .mono { font-family: 'Courier New', Courier, monospace; font-size: 8.5px; }
  .accion-I { font-weight: 700; }
  .accion-U { font-weight: 700; }
  .accion-D { font-weight: 700; text-decoration: underline; }

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
  <div class="header-report">Auditoria de Cambios en Notas</div>
  <div class="header-meta">Generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</div>
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
      <td class="mono">{{ $row[0] }}</td>
      <td>{{ $row[1] }}</td>
      <td>
        <span class="accion-{{ substr($row[2] === 'Inserción' ? 'I' : ($row[2] === 'Actualización' ? 'U' : 'D'), 0, 1) }}">
          {{ $row[2] }}
        </span>
      </td>
      <td>{{ $row[3] }}</td>
      <td class="mono">{{ $row[4] }}</td>
      <td class="mono">{{ $row[5] }}</td>
      <td class="mono">{{ $row[6] }}</td>
    </tr>
    @empty
    <tr><td colspan="7" style="text-align:center;padding:20px;color:#666;">Sin registros de auditoria.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>Universidad — Documento Confidencial</span>
  <span>Pág. <span class="page-number"></span></span>
</div>
</body>
</html>
