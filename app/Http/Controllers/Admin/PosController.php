<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class PosController extends Controller
{
    // Vista principal del POS
    public function index()
    {
        return view('admin.pos.index');
    }

    // Buscar orden por número
    public function findByNumber($orderNumber)
    {
        $order = Order::with(['customer', 'items.service', 'paymentMethod', 'paymentSubmethod'])
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }
}