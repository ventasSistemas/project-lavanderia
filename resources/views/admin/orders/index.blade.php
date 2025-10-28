<!-- views/admin/orders/index.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Gestión de Órdenes')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-cart-flatbed-suitcase text-primary me-2"></i> Gestión de Órdenes
            </h4>
            <p class="text-muted small mb-0">Administra todas las órdenes registradas</p>
        </div>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary shadow-sm px-3">
            <i class="fa-solid fa-plus me-1"></i> Nueva Orden
        </a>
    </div>

    <!-- Buscador -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex align-items-center gap-2">
                <input type="text" name="search" class="form-control" placeholder="Buscar por n° de orden o cliente..."
                       value="{{ request('search') }}" style="max-width: 300px;">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fa-solid fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Tabla de órdenes -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive rounded">
                <table class="table table-hover table-nowrap align-middle mb-0 rounded">
                    <thead class="table-primary text-white">
                        <tr>
                            <th>#</th>
                            <th>N° de Orden</th>
                            <th>Cliente</th>
                            <th>Empleado</th>
                            <th>Sucursal</th>
                            <th>Estado Orden</th>
                            <th>Estado Pago</th>
                            <th>Total</th>
                            <th>Recepción</th>
                            <th>Entrega</th>
                            <th class="pe-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                <td><span class="fw-semibold text-primary">{{ $order->order_number }}</span></td>
                                <td>{{ $order->customer->full_name ?? '-' }}</td>
                                <td>{{ $order->employee->full_name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $order->branch->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->status)
                                        <form method="POST" action="{{ route('admin.orders.changeStatus') }}" class="d-flex align-items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="order_ids[]" value="{{ $order->id }}">

                                            @php
                                                $currentStatus = strtolower($order->status->name);
                                                $allowedTransitions = [
                                                    'pendiente' => ['en proceso'],
                                                    'en proceso' => ['terminado'],
                                                    'terminado' => [],
                                                ];
                                            @endphp

                                            <select name="new_status_id"
                                                    class="form-select form-select-sm w-auto"
                                                    onchange="this.form.submit()"
                                                    {{ empty($allowedTransitions[$currentStatus]) ? 'disabled' : '' }}>
                                                
                                                {{-- Estado actual visible y seleccionado --}}
                                                <option value="{{ $order->status->id }}" selected>
                                                    {{ ucfirst($order->status->name) }}
                                                </option>

                                                {{-- Mostrar solo opciones válidas según estado actual --}}
                                                @foreach($statuses as $status)
                                                    @if(in_array(strtolower($status->name), $allowedTransitions[$currentStatus] ?? []))
                                                        <option value="{{ $status->id }}">{{ ucfirst($status->name) }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">Sin estado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->payment_status === 'paid')
                                        <span class="badge bg-success-subtle text-success">Pagado</span>
                                    @elseif($order->payment_status === 'partial')
                                        <span class="badge bg-warning-subtle text-dark">Incompleto</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Pendiente</span>
                                    @endif
                                </td>
                                <td>S/{{ number_format($order->final_total, 2) }}</td>
                                <td>{{ $order->receipt_date ?? '-' }}</td>
                                <td>{{ $order->delivery_date ?? '-' }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info me-1" title="Ver">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Deseas eliminar esta orden?')" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">No se encontraron órdenes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="card-footer bg-white text-end">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection