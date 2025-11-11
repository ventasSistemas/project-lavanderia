<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 25px;
        }
        header {
            text-align: center; 
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        header img {
            width: 100px; 
            height: auto;
            display: block;
            margin: 0 auto 8px auto; 
        }

        header .title {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        header .subtitle {
            font-size: 13px;
            color: #666;
        }
        .filters {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .filters p {
            margin: 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 15px;
            font-size: 13px;
        }
        footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #888;
        }
    </style>
</head>
<body>

<header>
    <!--<img src="{{ public_path('images/logo/laundry-logo.jpg') }}" alt="Logo">-->
    <div>   
        <div class="title">Laundry System</div>
        <div class="subtitle">Reporte de Ventas Detallado</div>
    </div>
</header>

<div class="filters">
    <p><strong>Rango de fechas:</strong> {{ $fechaInicio }} al {{ $fechaFin }}</p>
    @if(!empty($sucursalSeleccionada))
        <p><strong>Sucursal:</strong> {{ $sucursalSeleccionada->name }}</p>
    @else
        <p><strong>Sucursal:</strong> Todas</p>
    @endif

    @if(!empty($empleadoSeleccionado))
        <p><strong>Empleado:</strong> {{ $empleadoSeleccionado->name }}</p>
    @else
        <p><strong>Empleado:</strong> Todos</p>
    @endif
</div>

<table>
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>N°</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Método Pago</th>
            <th>Sucursal</th>
            <th>Total (S/)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($ventas as $venta)
            <tr>
                <td>{{ $venta['tipo'] }}</td>
                <td>{{ \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y H:i') }}</td>
                <td>{{ $venta['numero'] }}</td>
                <td>{{ $venta['cliente'] }}</td>
                <td>{{ $venta['estado'] }}</td>
                <td>{{ $venta['metodo_pago'] }}</td>
                <td>{{ $venta['sucursal'] }}</td>
                <td style="text-align:right;">{{ number_format($venta['total'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#999;">No se encontraron registros en este rango</td>
            </tr>
        @endforelse
    </tbody>
</table>

<p class="total">
    Total General: <span style="color: #28a745;">S/. {{ number_format($total, 2) }}</span>
</p>

<footer>
    Generado el {{ now()->format('d/m/Y H:i') }}
</footer>

</body>
</html>
