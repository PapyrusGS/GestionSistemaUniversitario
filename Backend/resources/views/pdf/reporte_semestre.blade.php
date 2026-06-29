<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Académico Semestral</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #0284c7;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0 0 6px 0;
            color: #0369a1;
            text-transform: uppercase;
        }
        .header .meta {
            color: #555;
            font-size: 11px;
        }
        .summary-container {
            margin-bottom: 25px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 12px 15px;
        }
        .summary-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
            color: #0369a1;
        }
        .summary-grid {
            width: 100%;
        }
        .summary-grid td {
            width: 25%;
            padding: 4px 0;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .summary-label {
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.data-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        table.data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .font-mono {
            font-family: monospace;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-aprobado {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-reprobado {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-sin-registro {
            background-color: #f1f5f9;
            color: #475569;
        }
        .txt-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Sistema Universitario</h1>
        <div class="meta">
            <strong>Reporte:</strong> Reporte Académico Semestral (HU-DOC-08)<br>
            <strong>Fecha de Generación:</strong> {{ $fecha }}<br>
            <strong>Docente:</strong> {{ $docente_nombre }}<br>
            <strong>Semestre/Periodo:</strong> {{ $periodo_nombre }}
        </div>
    </div>

    <div class="summary-container">
        <div class="summary-title">Resumen Semestral</div>
        <table class="summary-grid">
            <tr>
                <td>
                    <span class="summary-label">Total Estudiantes</span><br>
                    <span class="summary-value">{{ $summary['total_estudiantes'] }}</span>
                </td>
                <td>
                    <span class="summary-label">Aprobados</span><br>
                    <span class="summary-value">{{ $summary['total_aprobados'] }}</span>
                </td>
                <td>
                    <span class="summary-label">Reprobados</span><br>
                    <span class="summary-value">{{ $summary['total_reprobados'] }}</span>
                </td>
                <td>
                    <span class="summary-label">Promedio General</span><br>
                    <span class="summary-value">{{ number_format($summary['promedio_general'], 2) }} pts</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Materia</th>
                <th class="txt-center" style="width: 15%;">Nota</th>
                <th class="txt-center" style="width: 25%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td><strong>{{ $row['nombreCompleto'] }}</strong></td>
                    <td>{{ $row['materia_nombre'] }}</td>
                    <td class="txt-center font-mono">
                        {{ $row['nota'] !== null ? number_format($row['nota'], 1) : '-' }}
                    </td>
                    <td class="txt-center">
                        @if($row['estadoAcademico'] === 'APROBADO')
                            <span class="badge badge-aprobado">APROBADO</span>
                        @elseif($row['estadoAcademico'] === 'REPROBADO')
                            <span class="badge badge-reprobado">REPROBADO</span>
                        @else
                            <span class="badge badge-sin-registro">SIN REGISTRO</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            @if(count($rows) === 0)
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px;">
                        No existen registros académicos para este periodo.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Generado automáticamente por el Sistema Universitario. Página 1 de 1
    </div>

</body>
</html>
