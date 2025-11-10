<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\PaymentMethod;
use App\Models\CashMovement;
use App\Models\CashRegister;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::with('submethods')->get();
        return response()->json($methods);
    }

    /** 
     * Registrar nueva venta de productos
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Verificar caja abierta
            $cashRegister = CashRegister::where('user_id', Auth::id())
                ->where('status', 'open')
                ->first();

            if (!$cashRegister) {
                return response()->json(['error' => 'No tienes una caja abierta.'], 400);
            }

            // Obtener sucursal y su letra
            $branch = Auth::user()->branch ?? null;
            $branchLetter = $branch?->code_letter ?? 'A';

            // Buscar última venta de esa sucursal
            $lastSale = Sale::where('branch_id', $branch?->id)
                ->where('order_number', 'LIKE', "PRO-{$branchLetter}-%")
                ->latest('id')
                ->first();

            // Calcular siguiente número correlativo
            $nextNumber = $lastSale ? intval(substr($lastSale->order_number, 7)) + 1 : 1;
            $orderNumber = 'PRO-' . $branchLetter . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Validar datos
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

            // Crear la venta
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
                'branch_id' => $branch?->id,
                'employee_id' => Auth::id(),
            ]);

            // Detalles de venta
            foreach ($data['items'] as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'complementary_product_id' => $item['id'],
                    'quantity' => $item['cantidad'],
                    'price' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio'],
                ]);
            }

            // Movimiento en caja
            CashMovement::create([
                'cash_register_id' => $cashRegister->id,
                'user_id' => Auth::id(),
                'type' => 'sale',
                'amount' => $sale->total,
                'concept' => "Venta #{$orderNumber}",
                'movement_date' => now(),
            ]);

            $cashRegister->increment('total_sales', $sale->total);

            DB::commit();

            return response()->json([
                'success' => true,
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

    /**
     * Devuelve el siguiente número de orden para la sucursal del usuario
     */
    public function nextOrderNumber()
    {
        $branch = Auth::user()->branch ?? null;
        $branchLetter = $branch?->code_letter ?? 'A';

        $lastSale = Sale::where('branch_id', $branch?->id)
            ->where('order_number', 'LIKE', "PRO-{$branchLetter}-%")
            ->latest('id')
            ->first();

        $nextNumber = $lastSale ? intval(substr($lastSale->order_number, 7)) + 1 : 1;
        $orderNumber = 'PRO-' . $branchLetter . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['next_order_number' => $orderNumber]);
    }
}