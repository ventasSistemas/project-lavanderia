<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * Mostrar listado de estados de pedidos.
     */
    public function index(Request $request)
    {
        $query = OrderStatus::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $orderStatuses = $query->orderBy('id', 'desc')->paginate(10); 

        return view('admin.order_status.index', compact('orderStatuses'));
    }

    /**
     * Guardar nuevo estado de pedido.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:order_status,name',
            'description' => 'nullable|string',
            'color_code' => 'nullable|string|max:10',
        ]);

        OrderStatus::create($request->only('name', 'description', 'color_code'));

        return redirect()->back()->with('success', 'Order status created successfully.');
    }

    /**
     * Actualizar estado de pedido existente.
     */
    public function update(Request $request, OrderStatus $orderStatus)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:order_status,name,' . $orderStatus->id,
            'description' => 'nullable|string',
            'color_code' => 'nullable|string|max:10',
        ]);

        $orderStatus->update($request->only('name', 'description', 'color_code'));

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Eliminar estado de pedido.
     */
    public function destroy(OrderStatus $orderStatus)
    {
        $orderStatus->delete();

        return redirect()->back()->with('success', 'Order status deleted successfully.');
    }
}