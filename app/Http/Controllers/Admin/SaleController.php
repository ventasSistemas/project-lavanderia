<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::with('submethods')->get();
        return response()->json($methods);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Generar número de orden automáticamente
            $lastSale = Sale::latest('id')->first();
            $nextNumber = $lastSale ? intval(substr($lastSale->order_number, 4)) + 1 : 1;
            $orderNumber = 'ORD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $data = $request->validate([
                'sale_date' => 'required|date',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'amount_received' => 'nullable|numeric|min:0',
                'change_given' => 'nullable|numeric|min:0',
                'payment_method_id' => 'nullable|exists:payment_methods,id',
                'payment_submethod_id' => 'nullable|exists:payment_submethods,id',
                'items' => 'required|array',
                'items.*.id' => 'required|exists:complementary_products,id',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.precio' => 'required|numeric|min:0',
            ]);

            $sale = Sale::create([
                'order_number' => $orderNumber,
                'sale_date' => $data['sale_date'],
                'subtotal' => $data['subtotal'],
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'total' => $data['total'],
                'amount_received' => $data['amount_received'] ?? null,
                'change_given' => $data['change_given'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'payment_submethod_id' => $data['payment_submethod_id'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'complementary_product_id' => $item['id'],
                    'quantity' => $item['cantidad'],
                    'price' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio'],
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Venta registrada correctamente',
                'order_number' => $orderNumber
            ], 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al registrar la venta',
                'details' => $th->getMessage()
            ], 500);
        }
    }

    public function nextOrderNumber()
    {
        $lastSale = Sale::latest('id')->first();
        $nextNumber = $lastSale ? intval(substr($lastSale->order_number, 4)) + 1 : 1;
        $orderNumber = 'ORD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['next_order_number' => $orderNumber]);
    }

}