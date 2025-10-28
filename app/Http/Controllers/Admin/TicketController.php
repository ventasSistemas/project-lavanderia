<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use PDF; // usaremos DomPDF

class TicketController extends Controller
{
    public function show($id)
    {
        $order = Order::with(['customer', 'branch', 'items.service'])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.ticket', compact('order'));
        return $pdf->stream("Ticket_{$order->order_number}.pdf");
    }
}
