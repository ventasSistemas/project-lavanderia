<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Branch;
use App\Models\Service;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'employee', 'branch', 'status'])
            ->latest()
            ->get();

        $customers = Customer::all();
        $employees = User::whereHas('role', fn($q) => $q->where('name', 'Employee'))->get();
        $branches = Branch::all();
        $statuses = OrderStatus::all();

        return view('admin.orders.index', compact('orders', 'customers', 'employees', 'branches', 'statuses'));
    }

    public function create()
    {
        $customers = Customer::all();
        $branches = Branch::all();
        $statuses = OrderStatus::all();
        $categories = ServiceCategory::with('services')->get();

        return view('admin.orders.create', compact('customers', 'branches', 'statuses', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'order_items' => 'required|array|min:1',
            'order_items.*.service_id' => 'required|exists:services,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                'customer_id' => $request->customer_id,
                'employee_id' => Auth::id(), 
                'branch_id' => $request->branch_id,
                'order_status_id' => $request->order_status_id ?? 1,
                'receipt_date' => now(),
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'total_amount' => 0,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'final_total' => 0,
            ]);

            $total = 0;

            foreach ($request->order_items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            $finalTotal = $total - $order->discount + $order->tax;
            $order->update([
                'total_amount' => $total,
                'final_total' => $finalTotal,
            ]);

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }


    public function show(Order $order)
    {
        $order->load(['items.service', 'customer', 'employee', 'status', 'branch']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['items.service', 'customer', 'branch', 'status']);
        $customers = Customer::all();
        $branches = Branch::all();
        $services = Service::all();
        $statuses = OrderStatus::all();

        return view('admin.orders.edit', compact('order', 'customers', 'branches', 'services', 'statuses'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_status,id',
            'payment_status' => 'required|in:pending,paid,partial',
        ]);

        $order->update([
            'order_status_id' => $request->order_status_id,
            'payment_status' => $request->payment_status,
            'delivery_date' => $request->delivery_date,
        ]);

        return back()->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Order deleted successfully.');
    }
}