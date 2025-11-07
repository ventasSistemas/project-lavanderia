@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Reporte de Ventas
            </h4>
            <p class="text-muted small mb-0">Consulta las ventas de órdenes y productos registradas en el sistema</p>
        </div>
    </div>

    <!-- Filtros de Fecha -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label fw-semibold">Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}" class="form-control shadow-sm">
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label fw-semibold">Hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}" class="form-control shadow-sm">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary shadow-sm w-100">
                        <i class="fa-solid fa-search me-2"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold text-secondary mb-0">
                    Ventas registradas del {{ $fechaInicio }} al {{ $fechaFin }}
                </h6>
            </div>
            <div class="table-responsive rounded">
                <table class="table table-hover table-nowrap align-middle mb-0">
                    <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td class="ps-3">{{ $venta['tipo'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta['numero'] }}</td>
                            <td>{{ $venta['cliente'] }}</td>
                            <td>{{ $venta['estado'] }}</td>
                            <td>{{ $venta['metodo_pago'] }}</td>
                            @if(Auth::user()->role->name !== 'employee')
                                <td>{{ $venta['sucursal'] }}</td>
                            @endif
                            <td class="fw-bold text-end pe-3">S/. {{ number_format($venta['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No se encontraron ventas en el rango seleccionado</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top text-end fw-bold">
                Total General: <span class="text-success">S/. {{ number_format($total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
