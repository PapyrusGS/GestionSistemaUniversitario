<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Rendimiento Académico</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #1a202c; background: #fff; }
  .header { padding: 18px 24px 14px; border-bottom: 3px solid #2b6cb0; margin-bottom: 18px; }
  .header h1 { font-size: 18px; color: #1a365d; font-weight: 700; }
  .header p { font-size: 10px; color: #718096; margin-top: 3px; }
  .meta { display: flex; justify-content: space-between; font-size: 9px; color: #718096; padding: 0 24px 14px; }
  table { width: 100%; border-collapse: collapse; margin: 0 0 20px; }
  thead th {
    background: #2b6cb0; color: #fff;
    padding: 8px 10px; text-align: left;
    font-size: 8.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
  }
  tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; color: #2d3748; }
  tbody tr:nth-child(even) { background: #ebf8ff; }
  .badge {
    display: inline-block; padding: 2px 8px; border-radius: 20px;
    font-size: 8px; font-weight: 700;
  }
  .badge-ok  { background: #c6f6d5; color: #22543d; }
  .badge-warn { background: #fefcbf; color: #744210; }
  .badge-none { background: #e2e8f0; color: #4a5568; }
  .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding: 6px 24px; font-size: 8px; color: #a0aec0; display: flex; justify-content: space-between; }
  .page-number:after { content: counter(page); }
</style>
</head>
<body>
<div class="header">
  <h1>📊 Reporte de Rendimiento Académico</h1>
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
    @foreach($data as $row)
    <tr>
      @foreach($row as $i => $celda)
        @if($i === 5)
          <td>
            @if($celda === 'Sin notas')
              <span class="badge badge-none">{{ $celda }}</span>
            @elseif((float)$celda >= 51)
              <span class="badge badge-ok">{{ $celda }}</span>
            @else
              <span class="badge badge-warn">{{ $celda }}</span>
            @endif
          </td>
        @else
          <td>{{ $celda ?? '—' }}</td>
        @endif
      @endforeach
    </tr>
    @endforeach
  </tbody>
</table>

<div class="footer">
  <span>Universidad — Documento Confidencial</span>
  <span>Página <span class="page-number"></span></span>
</div>
</body>
</html>
