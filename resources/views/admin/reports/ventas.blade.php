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

                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label fw-semibold">Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                        value="{{ $fechaInicio }}" class="form-control shadow-sm">
                </div>

                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label fw-semibold">Hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin"
                        value="{{ $fechaFin }}" class="form-control shadow-sm">
                </div>

                @if(Auth::user()->role->name === 'admin')
                    <div class="col-md-3">
                        <label for="sucursal_id" class="form-label fw-semibold">Sucursal</label>
                        <select name="sucursal_id" id="sucursal_id" class="form-select shadow-sm">
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
                        <label for="empleado_id" class="form-label fw-semibold">Empleado</label>
                        <select name="empleado_id" id="empleado_id" class="form-select shadow-sm">
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

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm w-100">
                        <i class="fa-solid fa-search me-2"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.reports.ventas') }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
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

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <script>
        $(function () {
            // --- Inicialización sucursal
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

            // --- Inicialización empleado
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
