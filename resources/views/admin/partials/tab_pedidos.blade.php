@if ($pedidos->isEmpty())
    <p class="text-muted">No hay órdenes registradas.</p>
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
                    <th>Fecha de Entrega</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidos as $order)
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
                    <td>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
