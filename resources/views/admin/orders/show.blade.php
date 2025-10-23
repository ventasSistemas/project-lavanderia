@extends('admin.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Detalle de la Orden</h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="fw-bold">Número de Orden:</h6>
                    <p>{{ $order->order_number }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Estado:</h6>
                    <span class="badge bg-{{ $order->status->color ?? 'secondary' }}">{{ ucfirst($order->status->name) }}</span>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Cliente:</h6>
                    <p>{{ $order->customer->full_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Registrado por:</h6>
                    <p>{{ $order->employee->full_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Sucursal:</h6>
                    <p>{{ $order->branch->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Fecha de Recepción:</h6>
                    <p>{{ $order->receipt_date ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Fecha de Entrega:</h6>
                    <p>{{ $order->delivery_date ?? '-' }}</p>
                </div>
            </div>

            <h5 class="fw-bold mt-4">Orden de Servicios</h5>
            <table class="table table-bordered mt-2">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Servicios</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>SubTotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->service->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Subtotal</th>
                        <th>${{ number_format($order->total_amount, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Discount</th>
                        <th>-${{ number_format($order->discount, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Tax</th>
                        <th>${{ number_format($order->tax, 2) }}</th>
                    </tr>
                    <tr class="fw-bold">
                        <th colspan="4" class="text-end">Total</th>
                        <th>${{ number_format($order->final_total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-3">
                <h6 class="fw-bold">Notes:</h6>
                <p>{{ $order->notes ?? 'No notes provided.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection