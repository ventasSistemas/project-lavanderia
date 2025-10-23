@extends('admin.layouts.app')

@section('title', 'Orders List')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Registro de Ordenes</h4>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Orden
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Numero de Orden</th>
                        <th>Cliente</th>
                        <th>Registrado por</th>
                        <th>Sucursal</th>
                        <th>Estado Orden</th>
                        <th>Estado Pago</th>
                        <th>Total</th>
                        <th>Fecha de Registro</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="fw-semibold text-primary">{{ $order->order_number }}</span></td>
                            <td>{{ $order->customer->full_name ?? '-' }}</td>
                            <td>{{ $order->employee->full_name ?? '-' }}</td>
                            <td>{{ $order->branch->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status->color ?? 'secondary' }}">
                                    {{ ucfirst($order->status->name) }}
                                </span>
                            </td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($order->payment_status === 'partial')
                                    <span class="badge bg-warning text-dark">Incompleto</span>
                                @else
                                    <span class="badge bg-danger">Pendiente</span>
                                @endif
                            </td>
                            <td>${{ number_format($order->final_total, 2) }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info" title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
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
                            <td colspan="10" class="text-center text-muted py-3">No se encontraron ordenes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
