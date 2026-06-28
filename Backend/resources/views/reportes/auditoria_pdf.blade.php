<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Auditoría de Notas</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9px; color: #1a202c; background: #fff; }
  .header { padding: 16px 24px 12px; border-bottom: 3px solid #c05621; margin-bottom: 14px; }
  .header h1 { font-size: 18px; color: #7b341e; font-weight: 700; }
  .header p  { font-size: 9px; color: #718096; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin: 0 0 20px; }
  thead th {
    background: #c05621; color: #fff;
    padding: 7px 8px; text-align: left;
    font-size: 8px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
  }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; word-break: break-word; }
  tbody tr:nth-child(even) { background: #fffaf0; }
  .ins { color: #2f855a; font-weight: 700; }
  .upd { color: #2b6cb0; font-weight: 700; }
  .del { color: #c53030; font-weight: 700; }
  .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding: 6px 24px; font-size: 8px; color: #a0aec0; display: flex; justify-content: space-between; }
  .page-number:after { content: counter(page); }
</style>
</head>
<body>
<div class="header">
  <h1>🔍 Auditoría de Cambios en Notas</h1>
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
    <tr>
      <td>{{ $row[0] }}</td>
      <td>{{ $row[1] }}</td>
      <td>
        @if($row[2] === 'Inserción')   <span class="ins">➕ {{ $row[2] }}</span>
        @elseif($row[2] === 'Actualización') <span class="upd">✏ {{ $row[2] }}</span>
        @elseif($row[2] === 'Eliminación')   <span class="del">🗑 {{ $row[2] }}</span>
        @else {{ $row[2] }}
        @endif
      </td>
      <td>{{ $row[3] }}</td>
      <td>{{ $row[4] }}</td>
      <td>{{ $row[5] }}</td>
      <td>{{ $row[6] }}</td>
    </tr>
    @empty
    <tr><td colspan="7" style="text-align:center;color:#718096;padding:20px;">Sin registros de auditoría.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>Universidad — Documento Confidencial</span>
  <span>Página <span class="page-number"></span></span>
</div>
</body>
</html>
