@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Tarjetas de resumen --}}
    <div class="row g-4 mb-4">
        @foreach ([
            ['icon' => 'fa-hourglass-half', 'color' => 'warning', 'label' => 'Pendientes', 'count' => $estadoPedidos['Pendiente']],
            ['icon' => 'fa-gear', 'color' => 'info', 'label' => 'En proceso', 'count' => $estadoPedidos['En proceso']],
            ['icon' => 'fa-box-open', 'color' => 'success', 'label' => 'Terminados', 'count' => $estadoPedidos['Terminado']],
            ['icon' => 'fa-truck', 'color' => 'primary', 'label' => 'Entregados', 'count' => $estadoPedidos['Entregado']]
        ] as $card)
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 hover-card text-center p-3">
                <div class="card-body">
                    <i class="fa-solid {{ $card['icon'] }} fa-2x text-{{ $card['color'] }} mb-2"></i>
                    <h6 class="text-muted">{{ $card['label'] }}</h6>
                    <h2 class="fw-bold text-dark">{{ $card['count'] }}</h2>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Gráficos --}}
    <div class="row g-4">
        {{-- Gráfico de Ganancias --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fa-solid fa-chart-line me-2 text-primary"></i>
                        Ganancias del Día, Semana, Mes y Año
                    </h6>
                    <canvas id="chartGanancias" height="180"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfico de Estado de Pedidos --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fa-solid fa-chart-pie me-2 text-success"></i>
                        Estado de Pedidos
                    </h6>
                    <canvas id="chartPedidos" height="180"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfico de Ventas de los Últimos 7 Días --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fa-solid fa-calendar-week me-2 text-warning"></i>
                        Ventas de la Última Semana
                    </h6>
                    <canvas id="chartVentasSemana" height="230"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de pedidos del día --}}
    <div class="card shadow-sm border-0 mt-4 rounded-3">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-calendar-day me-2 text-secondary"></i>Entregas de hoy</h5>
            @if ($pedidosDelDia->isEmpty())
                <p class="text-muted">No hay órdenes registradas hoy.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Empleado</th>
                                <th>Estado</th>
                                <th>Total (S/)</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pedidosDelDia as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer->full_name ?? 'N/A' }}</td>
                                <td>{{ $order->employee->full_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge 
                                        @switch($order->status->name)
                                            @case('pending') bg-warning @break
                                            @case('processing') bg-info @break
                                            @case('ready') bg-success @break
                                            @case('delivered') bg-primary @break
                                            @default bg-secondary
                                        @endswitch">
                                        {{ ucfirst($order->status->name) }}
                                    </span>
                                </td>
                                <td>{{ number_format($order->final_total, 2) }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gráfico de Ganancias 
    const ctxGanancias = document.getElementById('chartGanancias');
    new Chart(ctxGanancias, {
        type: 'bar',
        data: {
            labels: ['Día', 'Semana', 'Mes', 'Año'],
            datasets: [{
                label: 'Ganancias (S/)',
                data: [{{ $gananciasDia }}, {{ $gananciasSemana }}, {{ $gananciasMes }}, {{ $gananciasAnio }}],
                backgroundColor: ['#007bff', '#17a2b8', '#28a745', '#ffc107'],
            }]
        }
    });

    // Gráfico de Estado de Pedidos
    const ctxPedidos = document.getElementById('chartPedidos');
    new Chart(ctxPedidos, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($estadoPedidos)),
            datasets: [{
                data: @json(array_values($estadoPedidos)),
                backgroundColor: ['#ffc107', '#0dcaf0', '#28a745', '#007bff', '#dc3545'],
            }]
        }
    });

    // Gráfico de Ventas Últimos 7 Días 
    const ctxVentasSemana = document.getElementById('chartVentasSemana');
    if (ctxVentasSemana) {
        new Chart(ctxVentasSemana, {
            type: 'line',
            data: {
                labels: @json($fechas ?? []),
                datasets: [{
                    label: 'Ventas (S/)',
                    data: @json($ventasUltimos7Dias ?? []),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
@endpush

@push('styles')
<style>
    .hover-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush
