<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementaryProduct;
use App\Models\ComplementaryProductCategory;
use Illuminate\Support\Facades\Auth;

class ComplementaryProductController extends Controller
{
    public function index($category_id)
    {
        $user = Auth::user();

        // Validar acceso: el manager solo puede ver su categoría
        $category = ComplementaryProductCategory::where('id', $category_id)
            ->where(function ($q) use ($user) {
                if ($user->role->name === 'manager') {
                    $q->where('branch_id', $user->branch_id);
                }
            })
            ->firstOrFail();

        $products = ComplementaryProduct::where('complementary_product_category_id', $category_id)
            ->where(function ($q) use ($user) {
                if ($user->role->name === 'manager') {
                    $q->where('branch_id', $user->branch_id);
                }
            })
            ->latest()
            ->get();

        return view('admin.complementary_products.index', compact('category', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'complementary_product_category_id' => 'required|exists:complementary_product_categories,id',
            'name' => 'required|string|max:120',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'state' => 'required|in:active,inactive',
        ]);

        $user = Auth::user();

        // Verificar acceso
        $category = ComplementaryProductCategory::findOrFail($request->complementary_product_category_id);

        if ($user->role->name === 'manager' && $category->branch_id !== $user->branch_id) {
            abort(403, 'No tienes permiso para agregar productos a esta categoría.');
        }

        $data = $request->only([
            'complementary_product_category_id',
            'name',
            'price',
            'stock',
            'state'
        ]);

        if ($request->hasFile('image')) {
            $folder = public_path('images/complementary_products');
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/complementary_products/' . $imageName;
        }

        $data['branch_id'] = $user->role->name === 'manager' ? $user->branch_id : null;

        ComplementaryProduct::create($data);

        return redirect()->back()->with('success', 'Producto complementario creado correctamente.');
    }

    public function update(Request $request, ComplementaryProduct $product)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'state' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'state']);

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            $folder = public_path('images/complementary_products');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/complementary_products/' . $imageName;
        }

        $product->update($data);

        return redirect()->back()->with('success', 'Producto complementario actualizado correctamente.');
    }

    public function destroy(ComplementaryProduct $product)
    {
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }

        $product->delete();

        return redirect()->back()->with('success', 'Producto complementario eliminado correctamente.');
    }
}
