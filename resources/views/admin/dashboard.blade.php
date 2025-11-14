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
        {{-- Ganancias --}}
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

        {{-- Estado de Pedidos --}}
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

        {{-- Ventas Últimos 7 Días --}}
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

    <br>

    {{-- Pestañas de entregas centradas con estilo profesional --}}
    <div class="d-flex justify-content-center mb-4">
        <ul class="nav nav-pills shadow-sm rounded-pill p-1" id="entregasTabs" role="tablist" style="gap: 10px;">
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill {{ request('tab') === 'pasados' ? 'active' : '' }}" 
                id="pasados-tab" data-bs-toggle="tab" href="#pasados" role="tab">
                    Pasados
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill {{ request('tab') === 'hoy' || !request('tab') ? 'active' : '' }}" 
                id="hoy-tab" data-bs-toggle="tab" href="#hoy" role="tab">
                    Hoy <span class="badge bg-primary">{{ \Carbon\Carbon::now('America/Lima')->format('d/m/Y') }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link rounded-pill {{ request('tab') === 'manana' ? 'active' : '' }}" 
                id="manana-tab" data-bs-toggle="tab" href="#manana" role="tab">
                    Mañana <span class="badge bg-success">{{ \Carbon\Carbon::now('America/Lima')->addDay()->format('d/m/Y') }}</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- Contenido de las pestañas --}}
    <div class="tab-content">
        {{-- Pedidos Pasados --}}
        <div class="tab-pane fade {{ request('tab') === 'pasados' ? 'show active' : '' }}" id="pasados" role="tabpanel">
            @include('admin.partials.tab_pedidos', ['pedidos' => $pedidosPasados])
        </div>

        {{-- Pedidos Hoy --}}
        <div class="tab-pane fade {{ request('tab') === 'hoy' || !request('tab') ? 'show active' : '' }}" id="hoy" role="tabpanel">
            @include('admin.partials.tab_pedidos', ['pedidos' => $pedidosHoy])
        </div>

        {{-- Pedidos Mañana --}}
        <div class="tab-pane fade {{ request('tab') === 'manana' ? 'show active' : '' }}" id="manana" role="tabpanel">
            @include('admin.partials.tab_pedidos', ['pedidos' => $pedidosManana])
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Charts
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

    // Tabs
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('#entregasTabs .nav-link');
        const panes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                tabs.forEach(t => t.classList.remove('active'));
                panes.forEach(p => p.classList.remove('show', 'active'));
                this.classList.add('active');
                const target = document.querySelector(this.getAttribute('href'));
                target.classList.add('show', 'active');
            });
        });

        // Mostrar hoy al cargar
        document.querySelector('#hoy-tab').click();
    });
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

#entregasTabs .nav-link {
    font-weight: 500;
    padding: 8px 20px;
    transition: all 0.2s ease-in-out;
    color: #495057;
}
#entregasTabs .nav-link.active {
    background-color: #0d6efd;
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
#entregasTabs .nav-link:hover {
    background-color: rgba(13,110,253,0.1);
    color: #0d6efd;
}

.tab-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.badge {
    font-size: 0.8rem;
    margin-left: 5px;
}
</style>
@endpush
