<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductTransfer;
use App\Models\ComplementaryProduct;
use App\Models\User;
use App\Models\ProductNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductTransferController extends Controller
{
    public function index()
    {
        $transfers = ProductTransfer::with(['product', 'branch', 'sender', 'reviewer'])
            ->latest()
            ->get();

        return view('admin.transfers.index', compact('transfers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transfers' => 'required|array|min:1',
            'transfers.*.category_id' => 'required|exists:complementary_product_categories,id',
            'transfers.*.product_id' => 'required|exists:complementary_products,id',
            'transfers.*.branch_id' => 'required|exists:branches,id',
            'transfers.*.quantity' => 'required|integer|min:1',
        ]);

        $admin = Auth::user();

        if ($admin->role->name !== 'admin') {
            abort(403, 'Solo el administrador puede transferir productos.');
        }

        foreach ($request->transfers as $item) {
            $product = ComplementaryProduct::find($item['product_id']);

            if ($item['quantity'] > $product->stock) {
                return back()->with('error', "No hay suficiente stock de {$product->name}.");
            }

            // 🔹 Descontar stock global
            $product->decrement('stock', $item['quantity']);

            // 🔹 Registrar transferencia
            $transfer = ProductTransfer::create([
                'complementary_product_id' => $product->id,
                'branch_id' => $item['branch_id'],
                'quantity' => $item['quantity'],
                'sent_by' => $admin->id,
                'status' => 'pending',
            ]);

            // 🔹 Buscar el manager de esa sucursal
            $manager = User::where('branch_id', $item['branch_id'])
                ->whereHas('role', fn($q) => $q->where('name', 'manager'))
                ->first();

            // 🔹 Registrar notificación para el manager
            if ($manager) {
                ProductNotification::create([
                    'product_transfer_id' => $transfer->id,
                    'user_id' => $manager->id,
                    'message' => "Has recibido una nueva transferencia de {$product->name} ({$item['quantity']} unidades).",
                    'is_read' => false,
                ]);
            }
        }

        return back()->with('success', 'Transferencias enviadas correctamente y notificaciones enviadas a las sucursales.');
    }

    public function approve(ProductTransfer $transfer)
    {
        $transfer->update([
            'status' => 'accepted',
            'reviewed_by' => Auth::id(),
        ]);

        $original = $transfer->product;

        // ✅ Crear categoría en la sucursal si no existe
        $branchCategory = \App\Models\ComplementaryProductCategory::firstOrCreate(
            [
                'name' => $original->category->name,
                'branch_id' => $transfer->branch_id,
            ],
            [
                'description' => $original->category->description,
                'image' => $original->category->image,
            ]
        );

        // ✅ Crear producto en la sucursal si no existe
        $branchProduct = ComplementaryProduct::firstOrCreate(
            [
                'name' => $original->name,
                'complementary_product_category_id' => $branchCategory->id,
                'branch_id' => $transfer->branch_id,
            ],
            [
                'price' => $original->price,
                'stock' => 0,
                'image' => $original->image,
                'state' => 'active',
            ]
        );

        // ✅ Incrementar stock en la sucursal
        $branchProduct->increment('stock', $transfer->quantity);

        return back()->with('success', 'Transferencia aceptada y producto agregado correctamente a la sucursal.');
    }

    public function reject(ProductTransfer $transfer)
    {
        $transfer->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
        ]);

        // 🔹 Devolver stock global
        $transfer->product->increment('stock', $transfer->quantity);

        return back()->with('info', 'Transferencia rechazada y stock devuelto.');
    }

    public function getProducts($categoryId)
    {
        $user = Auth::user();

        $query = \App\Models\ComplementaryProduct::where('complementary_product_category_id', $categoryId);

        if ($user->role->name === 'admin') {
            $query->whereNull('branch_id');
        } else {
            $query->where('branch_id', $user->branch_id);
        }

        $products = $query->get(['id', 'name', 'stock']);

        return response()->json($products);
    }
}