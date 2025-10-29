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

    public function buscarCliente(Request $request)
    {
        $search = $request->input('q');

        $clientes = Customer::where('full_name', 'like', "%{$search}%")
            ->orWhereHas('orders', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            })
            ->with(['orders:id,order_number,customer_id'])
            ->take(10)
            ->get(['id', 'full_name']);

        $resultado = $clientes->map(function ($cliente) {
            return [
                'id' => $cliente->id,
                'full_name' => $cliente->full_name,
                'orders' => $cliente->orders->pluck('order_number'),
            ];
        });

        return response()->json($resultado);
    }

}