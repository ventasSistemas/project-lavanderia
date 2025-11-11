@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-chart-line text-primary me-2"></i> Reporte de Ventas
            </h3>
            <p class="text-muted mb-0">Visualiza y exporta las ventas de órdenes de servicio y productos</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al panel
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light fw-semibold text-primary">
            <i class="fa-solid fa-filter me-2"></i> Filtros de búsqueda
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label fw-semibold text-muted">Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                        value="{{ $fechaInicio }}" class="form-control form-control-sm shadow-sm border-primary-subtle">
                </div>

                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label fw-semibold text-muted">Hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin"
                        value="{{ $fechaFin }}" class="form-control form-control-sm shadow-sm border-primary-subtle">
                </div>

                @if(Auth::user()->role->name === 'admin')
                    <div class="col-md-3">
                        <label for="sucursal_id" class="form-label fw-semibold text-muted">Sucursal</label>
                        <select name="sucursal_id" id="sucursal_id" class="form-select form-select-sm shadow-sm">
                            <option value="all">— Todas las sucursales —</option>
                            @if($filtroSucursalId && isset($sucursales))
                                @php
                                    $sucursalSeleccionada = $sucursales->firstWhere('id', $filtroSucursalId);
                                @endphp
                                <option value="{{ $filtroSucursalId }}" selected>
                                    {{ $sucursalSeleccionada->name ?? 'Seleccionada' }}
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="empleado_id" class="form-label fw-semibold text-muted">Empleado</label>
                        <select name="empleado_id" id="empleado_id" class="form-select form-select-sm shadow-sm">
                            <option value="all">— Todos los empleados —</option>
                            @if($filtroEmpleadoId && isset($empleados))
                                @php
                                    $empleadoSeleccionado = $empleados->firstWhere('id', $filtroEmpleadoId);
                                @endphp
                                <option value="{{ $filtroEmpleadoId }}" selected>
                                    {{ $empleadoSeleccionado->name ?? 'Seleccionado' }}
                                </option>
                            @endif
                        </select>
                    </div>
                @endif

                <div class="col-12 d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                        <i class="fa-solid fa-search me-2"></i> Filtrar
                    </button>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reports.ventas.pdf', request()->all()) }}" 
                           class="btn btn-outline-danger shadow-sm">
                            <i class="fa-solid fa-file-pdf me-1"></i> Exportar PDF
                        </a>
                        <a href="{{ route('admin.reports.ventas.excel', request()->all()) }}" 
                           class="btn btn-outline-success shadow-sm">
                            <i class="fa-solid fa-file-excel me-1"></i> Exportar Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Paginación -->
        <div class="card-footer bg-white text-end">
            {{ $ventas->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Resultados -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold text-primary">
            <i class="fa-solid fa-table me-2"></i> Resultados del {{ $fechaInicio }} al {{ $fechaFin }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive rounded">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Método de Pago</th>
                            @if(Auth::user()->role->name !== 'employee')
                                <th>Sucursal</th>
                            @endif
                            <th class="text-end pe-3">Total (S/)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td class="ps-3">{{ $venta['tipo'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta['numero'] }}</td>
                            <td>{{ $venta['cliente'] }}</td>
                            <td><span class="badge bg-info-subtle text-dark">{{ $venta['estado'] }}</span></td>
                            <td>{{ $venta['metodo_pago'] }}</td>
                            @if(Auth::user()->role->name !== 'employee')
                                <td>{{ $venta['sucursal'] }}</td>
                            @endif
                            <td class="fw-bold text-end pe-3 text-success">
                                {{ number_format($venta['total'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> No se encontraron ventas en este rango.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top text-end fw-bold fs-6">
                Total General: <span class="text-success">S/. {{ number_format($total, 2) }}</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        $(function () {
            $('#sucursal_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Buscar sucursal...',
                allowClear: true,
                ajax: {
                    url: "{{ route('admin.reports.buscarSucursales') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({
                        results: [
                            { id: 'all', text: '— Todas las sucursales —' },
                            ...data.map(item => ({ id: item.id, text: item.name }))
                        ]
                    }),
                    cache: true
                },
                width: '100%'
            });

            $('#empleado_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Buscar empleado...',
                allowClear: true,
                ajax: {
                    url: "{{ route('admin.reports.buscarEmpleados') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({
                        results: [
                            { id: 'all', text: '— Todos los empleados —' },
                            ...data.map(item => ({ id: item.id, text: item.name }))
                        ]
                    }),
                    cache: true
                },
                width: '100%'
            });
        });
    </script>
@endpush
@endsection
