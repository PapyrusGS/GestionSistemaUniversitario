<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ocupación de Cursos</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9px; color: #1a202c; background: #fff; }
  .header { padding: 16px 24px 12px; border-bottom: 3px solid #2c7a7b; margin-bottom: 14px; }
  .header h1 { font-size: 18px; color: #234e52; font-weight: 700; }
  .header p  { font-size: 9px; color: #718096; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin: 0 0 20px; }
  thead th {
    background: #2c7a7b; color: #fff;
    padding: 8px 9px; text-align: left;
    font-size: 8px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
  }
  thead th:nth-child(6),
  thead th:nth-child(7),
  thead th:nth-child(8),
  thead th:nth-child(9) { text-align: center; }
  tbody td { padding: 7px 9px; border-bottom: 1px solid #e2e8f0; color: #2d3748; }
  tbody td:nth-child(6),
  tbody td:nth-child(7),
  tbody td:nth-child(8),
  tbody td:nth-child(9) { text-align: center; }
  tbody tr:nth-child(even) { background: #e6fffa; }
  .bar-wrap { width: 60px; height: 8px; background: #e2e8f0; border-radius: 4px; display: inline-block; vertical-align: middle; }
  .bar-fill { height: 8px; border-radius: 4px; }
  .pct-text { font-weight: 700; margin-left: 4px; vertical-align: middle; }
  .full   { color: #c53030; }
  .high   { color: #c05621; }
  .medium { color: #2b6cb0; }
  .low    { color: #276749; }
  .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding: 6px 24px; font-size: 8px; color: #a0aec0; display: flex; justify-content: space-between; }
  .page-number:after { content: counter(page); }
</style>
</head>
<body>
<div class="header">
  <h1>📋 Ocupación de Cursos</h1>
  <p>Sistema de Gestión Universitaria — Generado el {{ now()->format('d/m/Y H:i') }}</p>
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
    @php
      $pctRaw = (float) rtrim($row[8], '%');
      $color = $pctRaw >= 90 ? '#fc8181' : ($pctRaw >= 60 ? '#f6ad55' : ($pctRaw >= 30 ? '#63b3ed' : '#68d391'));
      $cls   = $pctRaw >= 90 ? 'full' : ($pctRaw >= 60 ? 'high' : ($pctRaw >= 30 ? 'medium' : 'low'));
    @endphp
    <tr>
      <td>{{ $row[0] }}</td>
      <td>{{ $row[1] }}</td>
      <td>{{ $row[2] }}</td>
      <td>{{ $row[3] }}</td>
      <td>{{ $row[4] }}</td>
      <td>{{ $row[5] }}</td>
      <td>{{ $row[6] }}</td>
      <td>{{ $row[7] }}</td>
      <td>
        <span class="bar-wrap">
          <span class="bar-fill" style="width:{{ min(100, $pctRaw) }}%;background:{{ $color }};"></span>
        </span>
        <span class="pct-text {{ $cls }}">{{ $row[8] }}</span>
      </td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center;color:#718096;padding:20px;">Sin cursos registrados.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>Universidad — Documento Confidencial</span>
  <span>Página <span class="page-number"></span></span>
</div>
</body>
</html>
