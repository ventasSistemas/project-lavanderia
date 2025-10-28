<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $order->order_number }}</title>
    <style>
        @media print {
            @page {
                size: 70mm 100mm; /* ancho fijo, alto dinámico */
                margin: 0;
            }

            html, body {
                width: 58mm;
                margin: 0;
                padding: 0;
                background: none;
            }

            /* Ocultar fondo blanco del navegador */
            body {
                background-color: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .ticket {
            width: 58mm;
            margin: 0 auto;
            padding: 2mm;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 4px;
            border-bottom: 1px dashed #999;
            padding-bottom: 2px;
        }

        .header h2 {
            margin: 0;
            font-size: 12px;
            color: #007bff;
        }

        .info {
            text-align: left;
            margin-top: 3px;
            margin-bottom: 4px;
        }

        .info p {
            margin: 1px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-top: 3px;
        }

        th, td {
            padding: 1px 0;
        }

        th {
            text-align: left;
            border-bottom: 1px solid #aaa;
        }

        td.text-end, th.text-end {
            text-align: right;
        }

        .totals {
            margin-top: 6px;
            border-top: 1px dashed #999;
            padding-top: 3px;
        }

        .totals table {
            width: 100%;
        }

        .footer {
            text-align: center;
            margin-top: 5px;
            font-size: 8px;
            border-top: 1px dashed #999;
            padding-top: 3px;
        }

        /* Evitar que el navegador agregue saltos */
        .ticket, table, .totals, .footer {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h2>CleanWash</h2>
            <small>Ticket de Orden</small><br>
            <strong>N° {{ $order->order_number }}</strong>
        </div>

        <div class="info">
            <p><strong>Cliente:</strong> {{ $order->customer->full_name ?? 'N/A' }}</p>
            <p><strong>Sucursal:</strong> {{ $order->branch->name ?? 'N/A' }}</p>
            <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Serv.</th>
                    <th class="text-end">Cant</th>
                    <th class="text-end">P/U</th>
                    <th class="text-end">Tot</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->service->name }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">S/{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">S/{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr><td>Subtotal:</td><td class="text-end">S/{{ number_format($order->total_amount, 2) }}</td></tr>
                <tr><td>Descuento:</td><td class="text-end">S/{{ number_format($order->discount, 2) }}</td></tr>
                <tr><td>Impuesto:</td><td class="text-end">S/{{ number_format($order->tax, 2) }}</td></tr>
                <tr><td><strong>Total:</strong></td><td class="text-end"><strong>S/{{ number_format($order->final_total, 2) }}</strong></td></tr>
                <tr><td>Monto pagado:</td><td class="text-end">S/{{ number_format($order->payment_amount ?? 0, 2) }}</td></tr>
                <tr><td>Vuelto:</td><td class="text-end">S/{{ number_format($order->payment_returned ?? 0, 2) }}</td></tr>
                <tr>
                    <td><strong>Estado Pago:</strong></td>
                    <td class="text-end">
                        @php
                            $labels = [
                                'paid' => 'Pagado',
                                'pending' => 'Pendiente',
                                'partial' => 'Incompleto'
                            ];
                            $estado = $labels[$order->payment_status ?? 'pending'] ?? 'Pendiente';
                        @endphp
                        <strong>{{ $estado }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <small>¡Gracias por confiar en CleanWash!</small><br>
            <small>{{ $order->branch->address ?? 'Dirección no registrada' }}</small>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>