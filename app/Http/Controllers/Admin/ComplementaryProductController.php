<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementaryProduct;
use App\Models\ComplementaryProductCategory;

class ComplementaryProductController extends Controller
{
    public function index($category_id)
    {
        $category = ComplementaryProductCategory::findOrFail($category_id);
        $products = ComplementaryProduct::where('complementary_product_category_id', $category_id)->latest()->get();

        return view('admin.complementary_products.index', compact('category', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'complementary_product_category_id' => 'required|exists:complementary_product_categories,id',
            'name' => 'required|string|max:120',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'state' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['complementary_product_category_id', 'name', 'price', 'state']);

        if ($request->hasFile('image')) {
            $folder = public_path('images/complementary_products');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/complementary_products/' . $imageName;
        }

        ComplementaryProduct::create($data);

        return redirect()->back()->with('success', 'Producto complementario creado correctamente.');
    }

    public function update(Request $request, ComplementaryProduct $product)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'state' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['name', 'price', 'state']);

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
