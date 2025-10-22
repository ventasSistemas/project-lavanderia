<!--views/admin/dashboard.blade.php-->
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Tarjetas de resumen --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 hover-card text-center p-3">
                <div class="card-body">
                    <i class="fa-solid fa-hourglass-half fa-2x text-warning mb-2"></i>
                    <h6 class="text-muted">Pendientes</h6>
                    <h2 class="fw-bold text-dark">15</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 hover-card text-center p-3">
                <div class="card-body">
                    <i class="fa-solid fa-gear fa-2x text-info mb-2"></i>
                    <h6 class="text-muted">Procesando</h6>
                    <h2 class="fw-bold text-dark">8</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 hover-card text-center p-3">
                <div class="card-body">
                    <i class="fa-solid fa-box-open fa-2x text-success mb-2"></i>
                    <h6 class="text-muted">Listo para entregar</h6>
                    <h2 class="fw-bold text-dark">12</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 hover-card text-center p-3">
                <div class="card-body">
                    <i class="fa-solid fa-truck fa-2x text-primary mb-2"></i>
                    <h6 class="text-muted">Entregados</h6>
                    <h2 class="fw-bold text-dark">20</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Ganancias del Día, Semana, Mes y Año</h6>
                    <canvas id="chartGanancias"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-chart-pie me-2 text-success"></i>Estado de Pedidos</h6>
                    <canvas id="chartPedidos"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de pedidos del día --}}
    <div class="card shadow-sm border-0 mt-4 rounded-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold"><i class="fa-solid fa-calendar-day me-2 text-secondary"></i>Entregas de hoy</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control" placeholder="Buscar pedido..." style="width: 200px;">
                    <select class="form-select">
                        <option>Todas</option>
                        <option>Pendiente</option>
                        <option>Procesando</option>
                        <option>Listo</option>
                        <option>Entregado</option>
                    </select>
                </div>
            </div>

            <p class="text-muted">No hay órdenes registradas aún.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // === Gráfico de Ganancias ===
    const ctxGanancias = document.getElementById('chartGanancias');
    new Chart(ctxGanancias, {
        type: 'bar',
        data: {
            labels: ['Día', 'Semana', 'Mes', 'Año'],
            datasets: [{
                label: 'Ganancias (S/.)',
                data: [120, 800, 3400, 25000],
                backgroundColor: ['#007bff', '#17a2b8', '#28a745', '#ffc107'],
            }]
        }
    });

    // === Gráfico de Estado de Pedidos ===
    const ctxPedidos = document.getElementById('chartPedidos');
    new Chart(ctxPedidos, {
        type: 'doughnut',
        data: {
            labels: ['Pendiente', 'Procesando', 'Listo', 'Entregado', 'Devuelto'],
            datasets: [{
                data: [15, 8, 12, 20, 2],
                backgroundColor: ['#ffc107', '#0dcaf0', '#28a745', '#007bff', '#dc3545'],
            }]
        }
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
</style>
@endpush
