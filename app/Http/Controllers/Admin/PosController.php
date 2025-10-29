<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplementaryProductCategory;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\PaymentMethod;
use App\Models\Order;

class PosController extends Controller
{
    // Vista principal del POS
    public function index()
    {
        $categorias = ComplementaryProductCategory::with('products')->get();
        $clientes = Customer::all();
        $paymentMethods = PaymentMethod::all();

        return view('admin.pos.index', compact('categorias', 'clientes', 'paymentMethods'));
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