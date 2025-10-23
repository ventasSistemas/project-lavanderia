@extends('admin.layouts.app')

@section('title', 'Editar Orden')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Editar Orden #{{ $order->id }}</h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="orderForm">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="customer_id" class="form-label fw-semibold">Cliente</label>
                        <select name="customer_id" id="customer_id" class="form-select" required>
                            <option value="">-- Seleccionar Cliente --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="branch_id" class="form-label fw-semibold">Sucursal</label>
                        <select name="branch_id" id="branch_id" class="form-select" required>
                            <option value="">-- Seleccionar Sucursal --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $order->branch_id == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="order_status_id" class="form-label fw-semibold">Estado Orden</label>
                        <select name="order_status_id" id="order_status_id" class="form-select">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ $order->order_status_id == $status->id ? 'selected' : '' }}>
                                    {{ ucfirst($status->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="discount" class="form-label fw-semibold">Descuento</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="{{ $order->discount }}">
                    </div>

                    <div class="col-md-6">
                        <label for="tax" class="form-label fw-semibold">Impuesto</label>
                        <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="{{ $order->tax }}">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label fw-semibold">Notas</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2">{{ $order->notes }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla dinámica de items --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light fw-semibold">Orden de Servicios</div>
            <div class="card-body">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Servicio</th>
                            <th width="120">Cantidad</th>
                            <th width="150">Precio Unitario ($)</th>
                            <th width="150">SubTotal ($)</th>
                            <th width="60">#</th>
                        </tr>
                    </thead>
                    <tbody id="orderItemsBody">
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td>
                                    <select name="order_items[{{ $index }}][service_id]" class="form-select service-select" required>
                                        <option value="">-- Seleccionar Servicio --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" data-price="{{ $service->price }}"
                                                {{ $item->service_id == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="order_items[{{ $index }}][quantity]" class="form-control quantity" min="1" value="{{ $item->quantity }}" required></td>
                                <td><input type="number" name="order_items[{{ $index }}][unit_price]" class="form-control unit-price" step="0.01" value="{{ $item->unit_price }}" required></td>
                                <td class="subtotal fw-semibold">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-x-lg"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-end">
                    <button type="button" id="addRow" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Agregar Servicio
                    </button>
                </div>
            </div>
        </div>

        {{-- Totales --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table">
                            <tr>
                                <th>Subtotal:</th>
                                <td id="subtotalDisplay">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Descuento:</th>
                                <td id="discountDisplay">${{ number_format($order->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Impuesto:</th>
                                <td id="taxDisplay">${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            <tr class="fw-bold">
                                <th>Total:</th>
                                <td id="totalDisplay">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Actualizar Orden
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Script dinámico --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ count($order->items) }};

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('#orderItemsBody tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            const sub = qty * price;
            row.querySelector('.subtotal').innerText = '$' + sub.toFixed(2);
            subtotal += sub;
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const total = subtotal - discount + tax;

        document.getElementById('subtotalDisplay').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('discountDisplay').innerText = '$' + discount.toFixed(2);
        document.getElementById('taxDisplay').innerText = '$' + tax.toFixed(2);
        document.getElementById('totalDisplay').innerText = '$' + total.toFixed(2);
    }

    // Precio automático
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('service-select')) {
            const price = e.target.selectedOptions[0].dataset.price || 0;
            e.target.closest('tr').querySelector('.unit-price').value = price;
            updateTotals();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price') ||
            e.target.id === 'discount' || e.target.id === 'tax') {
            updateTotals();
        }
    });

    // Agregar fila
    document.getElementById('addRow').addEventListener('click', function() {
        const tbody = document.getElementById('orderItemsBody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="order_items[${itemIndex}][service_id]" class="form-select service-select" required>
                    <option value="">-- Seleccionar Servicio --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="order_items[${itemIndex}][quantity]" class="form-control quantity" min="1" value="1" required></td>
            <td><input type="number" name="order_items[${itemIndex}][unit_price]" class="form-control unit-price" step="0.01" required></td>
            <td class="subtotal fw-semibold">$0.00</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-x-lg"></i></button></td>
        `;
        tbody.appendChild(newRow);
        itemIndex++;
    });

    // Eliminar fila
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            updateTotals();
        }
    });
});
</script>
@endpush
@endsection