<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Service;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function index($order_id)
    {
        $items = OrderItem::with('service')
            ->where('order_id', $order_id)
            ->get();

        $services = Service::all();

        return view('admin.orders.items', compact('items', 'order_id', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        OrderItem::create($request->all());
        return back()->with('success', 'Item added successfully.');
    }

    public function update(Request $request, OrderItem $orderItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $orderItem->update($request->all());
        return back()->with('success', 'Item updated successfully.');
    }

    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();
        return back()->with('success', 'Item deleted successfully.');
    }
}