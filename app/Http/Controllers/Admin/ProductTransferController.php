<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductTransfer;
use App\Models\ComplementaryProductCategory;
use App\Models\ComplementaryProduct;
use App\Models\User;
use App\Models\ProductNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductTransferController extends Controller
{
    /**
     * Listado de transferencias con buscador y paginación.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = ProductTransfer::with(['product', 'branch', 'sender', 'reviewer'])
            ->orderBy('id', 'desc');

        // Filtrar por sucursal si es manager
        if ($user->role->name === 'manager') {
            $query->where('branch_id', $user->branch_id);
        }

        // Filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('branch', function ($q3) use ($search) {
                    $q3->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // Paginación
        $transfers = $query->paginate(10)->appends(['search' => $search]);

        return view('admin.transfers.index', compact('transfers', 'search'));
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

            // Descontar stock global
            $product->decrement('stock', $item['quantity']);

            // Registrar transferencia
            $transfer = ProductTransfer::create([
                'complementary_product_id' => $product->id,
                'branch_id' => $item['branch_id'],
                'quantity' => $item['quantity'],
                'sent_by' => $admin->id,
                'status' => 'pending',
            ]);

            // Buscar el manager de esa sucursal
            $manager = User::where('branch_id', $item['branch_id'])
                ->whereHas('role', fn($q) => $q->where('name', 'manager'))
                ->first();

            // Registrar notificación para el manager
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

        // Marcar notificación como leída
        ProductNotification::where('product_transfer_id', $transfer->id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);

        $original = $transfer->product;

        // Crear categoría en la sucursal si no existe
        $branchCategory = ComplementaryProductCategory::firstOrCreate(
            [
                'name' => $original->category->name,
                'branch_id' => $transfer->branch_id,
            ],
            [
                'description' => $original->category->description,
                'image' => $original->category->image,
            ]
        );

        // Crear producto en la sucursal si no existe
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

        // Incrementar stock en la sucursal
        $branchProduct->increment('stock', $transfer->quantity);

        return back()->with('success', 'Transferencia aceptada y producto agregado correctamente a la sucursal.');
    }

    public function reject(ProductTransfer $transfer)
    {
        $transfer->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
        ]);

        // ✅ Marcar notificación como leída
        ProductNotification::where('product_transfer_id', $transfer->id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);

        // Devolver stock global
        $transfer->product->increment('stock', $transfer->quantity);

        return back()->with('info', 'Transferencia rechazada y stock devuelto.');
    }

    public function getProducts($categoryId)
    {
        $user = Auth::user();

        $query = ComplementaryProduct::where('complementary_product_category_id', $categoryId);

        if ($user->role->name === 'admin') {
            $query->whereNull('branch_id');
        } else {
            $query->where('branch_id', $user->branch_id);
        }

        $products = $query->get(['id', 'name', 'stock']);

        return response()->json($products);
    }

    /**
     * Marca una notificación como leída y redirige a transferencias.
     */
    public function markAsRead($id)
    {
        $notification = ProductNotification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        // ✅ Redirige a la ruta index que carga correctamente la vista
        return redirect()->route('admin.product-transfers.index');
    }
}