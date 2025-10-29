<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
//use PDF; 

class TicketController extends Controller
{
    public function show($id)
    {
        $order = Order::with(['customer', 'branch', 'items.service'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.orders.ticket', compact('order'));
        return $pdf->stream("Ticket_{$order->order_number}.pdf");
    }
}