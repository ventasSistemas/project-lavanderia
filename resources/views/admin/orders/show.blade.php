@extends('admin.layouts.app')

@section('title', 'Detalle de la Orden')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Detalle de la Orden #{{ $order->id }}</h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
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
                    <h6 class="fw-bold">Estado de la Orden:</h6>
                    <span class="badge text-white px-3 py-2"
                          style="background-color: {{ $order->status->color_code ?? '#6c757d' }};">
                        {{ ucfirst($order->status->name ?? 'Desconocido') }}
                    </span>
                </div>

                <div class="col-md-6 mt-3">
                    <h6 class="fw-bold">Estado de Pago:</h6>
                    @php
                        $paymentLabels = [
                            'paid' => 'Pagado',
                            'partial' => 'Incompleto',
                            'pending' => 'Pendiente',
                        ];

                        $paymentStyles = [
                            'paid' => 'bg-success-subtle text-success',
                            'partial' => 'bg-warning-subtle text-dark',
                            'pending' => 'bg-danger-subtle text-danger',
                        ];

                        $paymentStatus = $order->payment_status ?? 'pending';
                        $paymentLabel = $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus);
                        $paymentClass = $paymentStyles[$paymentStatus] ?? 'bg-secondary text-white';
                    @endphp

                    <span class="badge {{ $paymentClass }} px-3 py-2">
                        {{ $paymentLabel }}
                    </span>
                </div>

                <div class="col-md-6 mt-3">
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
                    <p>{{ \Carbon\Carbon::parse($order->receipt_date)->format('d/m/Y H:i') ?? '-' }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="fw-bold">Fecha de Entrega:</h6>
                    <p>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y H:i') ?? '-' }}</p>
                </div>
            </div>

            <hr>

            <h5 class="fw-bold mt-4">Servicios en la Orden</h5>
            <table class="table table-bordered mt-2">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Servicio</th>
                        <th>Cantidad</th>
                        <th>Precio Unit. (S/)</th>
                        <th>SubTotal (S/)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->service->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>S/{{ number_format($item->unit_price, 2) }}</td>
                            <td>S/{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Subtotal</th>
                        <th>S/{{ number_format($order->total_amount, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Descuento</th>
                        <th>-S/{{ number_format($order->discount, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Impuesto</th>
                        <th>S/{{ number_format($order->tax, 2) }}</th>
                    </tr>
                    <tr class="fw-bold border-top">
                        <th colspan="4" class="text-end">Total</th>
                        <th>S/{{ number_format($order->final_total, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end text-success">Monto Pagado</th>
                        <th class="text-success">S/{{ number_format($order->payment_amount ?? 0, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end text-danger">Vuelto</th>
                        <th class="text-danger">S/{{ number_format($order->payment_returned ?? 0, 2) }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-3">
                <h6 class="fw-bold">Notas:</h6>
                <p>{{ $order->notes ?? 'Sin notas adicionales.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection