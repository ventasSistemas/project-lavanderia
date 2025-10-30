<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplementaryProductCategory;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    // Vista principal del POS
    public function index()
    {
        $categorias = ComplementaryProductCategory::with('products')->get();
        $clientes = Customer::all();
        $orderStatuses = OrderStatus::all();
        $paymentMethods = PaymentMethod::all();

        return view('admin.pos.index', compact('categorias', 'orderStatuses', 'clientes', 'paymentMethods'));
    }

    public function buscarOrden(Request $request)
    {
        $search = $request->input('q');

        $ordenes = Order::with('customer:id,full_name')
            ->where('order_number', 'like', "%{$search}%")
            ->take(10)
            ->get(['id', 'order_number', 'customer_id']);

        $resultado = $ordenes->map(function ($orden) {
            return [
                'id' => $orden->id,
                'order_number' => $orden->order_number,
                'customer_name' => $orden->customer ? $orden->customer->full_name : 'Sin cliente',
            ];
        });

        return response()->json($resultado);
    }

    public function detalleOrden($id)
    {
        $order = Order::with([
            'items.service',
            'customer:id,full_name',
            'paymentMethod:id,name',
            'status:id,name'
        ])->find($id);

        if (!$order) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        return response()->json([
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_id' => $order->customer_id, 
            'customer_name' => $order->customer->full_name ?? 'Cliente desconocido',
            'order_status' => $order->status->name ?? 'Sin estado',
            'payment_status' => $order->payment_status ?? 'pendiente',
            'payment_amount' => $order->payment_amount ?? 0,
            'final_total' => $order->final_total ?? 0,
            'payment_method' => $order->paymentMethod->name ?? 'No especificado',
            'updated_at' => $order->updated_at->format('d/m/Y H:i'),
            'items' => $order->items->map(function ($item) {
                return [
                    'service_name' => $item->service->name ?? 'Sin nombre',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            }),
        ]);
    }

    public function guardarOrden(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'branch_id' => 'required|exists:branches,id',
                'employee_id' => 'nullable|exists:users,id',
                'payment_method_id' => 'nullable|exists:payment_methods,id',
                'payment_submethod_id' => 'nullable|exists:payment_submethods,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:complementary_products,id',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'final_total' => 'required|numeric|min:0',
            ]);

            // Calcular total
            $total = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);

            // Generar número de orden único
            $orderNumber = 'ORD-' . now()->format('Ymd-His');

            // Crear la orden principal
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $validated['customer_id'],
                'employee_id' => Auth::id(),
                'branch_id' => $validated['branch_id'],
                'payment_method_id' => $validated['payment_method_id'] ?? null,
                'payment_submethod_id' => $validated['payment_submethod_id'] ?? null,
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'total_amount' => $total,
                'final_total' => $validated['final_total'] ?? $total,
                'order_status_id' => 1, // Estado "Pendiente"
                'payment_status' => 'pending',
            ]);

            // Insertar los productos complementarios
            foreach ($validated['items'] as $item) {
                $order->details()->create([
                    'complementary_product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden registrada correctamente.',
                'order_number' => $order->order_number,
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '❌ Error al registrar la orden: ' . $e->getMessage(),
            ], 500);
        }
    }

}