<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementaryProductCategory;

class ComplementaryProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ComplementaryProductCategory::with('products')->latest()->get();
        return view('admin.complementary_product.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $folder = public_path('images/complementary_categories');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/complementary_categories/' . $imageName;
        }

        ComplementaryProductCategory::create($data);

        return redirect()->back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, ComplementaryProductCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $folder = public_path('images/complementary_categories');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/complementary_categories/' . $imageName;
        }

        $category->update($data);

        return redirect()->back()->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(ComplementaryProductCategory $category)
    {
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->delete();

        return redirect()->back()->with('success', 'Categoría eliminada correctamente.');
    }
}