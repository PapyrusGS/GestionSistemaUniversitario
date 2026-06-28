<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Kárdex Académico — {{ $cabecera['ci'] }}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #1a202c; background: #fff; }
  .header { padding: 16px 24px 12px; border-bottom: 3px solid #553c9a; margin-bottom: 14px; }
  .header h1 { font-size: 18px; color: #44337a; font-weight: 700; }
  .header p  { font-size: 9px; color: #718096; margin-top: 2px; }
  .card {
    margin: 0 0 18px;
    background: #faf5ff; border: 1px solid #d6bcfa;
    border-radius: 8px; padding: 12px 16px;
    display: flex; gap: 24px;
  }
  .card-field { flex: 1; }
  .card-label { font-size: 8px; font-weight: 700; text-transform: uppercase; color: #805ad5; letter-spacing: 0.06em; }
  .card-value { font-size: 11px; font-weight: 600; color: #2d3748; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin: 0 0 20px; }
  thead th {
    background: #553c9a; color: #fff;
    padding: 8px 10px; text-align: left;
    font-size: 8.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
  }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; color: #2d3748; }
  tbody tr:nth-child(even) { background: #faf5ff; }
  .aprobada  { color: #276749; font-weight: 700; }
  .reprobada { color: #9b2c2c; font-weight: 700; }
  .sin-nota  { color: #718096; }
  .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding: 6px 24px; font-size: 8px; color: #a0aec0; display: flex; justify-content: space-between; }
  .page-number:after { content: counter(page); }
</style>
</head>
<body>
<div class="header">
  <h1>🎓 Kárdex Académico</h1>
  <p>Sistema de Gestión Universitaria — Generado el {{ $fecha }}</p>
</div>

<div class="card">
  <div class="card-field">
    <div class="card-label">Estudiante</div>
    <div class="card-value">{{ $cabecera['nombre'] }}</div>
  </div>
  <div class="card-field">
    <div class="card-label">C.I.</div>
    <div class="card-value">{{ $cabecera['ci'] }}</div>
  </div>
  <div class="card-field">
    <div class="card-label">Correo</div>
    <div class="card-value">{{ $cabecera['correo'] }}</div>
  </div>
  <div class="card-field">
    <div class="card-label">Carrera</div>
    <div class="card-value">{{ $cabecera['carrera'] }}</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>Período</th>
      <th>Materia</th>
      <th>Semestre</th>
      <th>Nota Final</th>
      <th>Estado Académico</th>
    </tr>
  </thead>
  <tbody>
    @forelse($historial as $fila)
    <tr>
      <td>{{ $fila['periodo'] }}</td>
      <td>{{ $fila['materia'] }}</td>
      <td>{{ $fila['semestre'] }}</td>
      <td>{{ $fila['nota'] ?? '—' }}</td>
      <td>
        @if($fila['estadoAcademico'] === 'Aprobada')
          <span class="aprobada">✔ Aprobada</span>
        @elseif($fila['estadoAcademico'] === 'Reprobada')
          <span class="reprobada">✘ Reprobada</span>
        @else
          <span class="sin-nota">— Sin nota</span>
        @endif
      </td>
    </tr>
    @empty
    <tr><td colspan="5" style="text-align:center;color:#718096;padding:20px;">Sin historial académico registrado.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>Universidad — Documento Confidencial</span>
  <span>Página <span class="page-number"></span></span>
</div>
</body>
</html>
