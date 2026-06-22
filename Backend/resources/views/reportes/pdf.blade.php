<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Académico</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2d3748; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #1a202c; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #718096; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
        th { background-color: #edf2f7; color: #2d3748; font-weight: bold; text-transform: uppercase; font-size: 10px; letter-spacing: 0.05em; }
        tr:nth-child(even) { background-color: #f7fafc; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 9px; text-align: center; color: #a0aec0; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Generado Automáticamente</h1>
        <p>Sistema de Gestión Universitaria</p>
    </div>
    
    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @foreach($row as $cell)
                <td>{{ $cell }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Página <span class="page-number"></span> - Documento Confidencial
    </div>
</body>
</html>
