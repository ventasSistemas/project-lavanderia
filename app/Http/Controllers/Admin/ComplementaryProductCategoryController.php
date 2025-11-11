<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementaryProductCategory;
use Illuminate\Support\Facades\Auth;

class ComplementaryProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name === 'admin') {
            // Obtener lista de sucursales para el desplegable
            $branches = \App\Models\Branch::orderBy('name')->get();

            // Verificar si seleccionó una sucursal
            $selectedBranch = $request->get('branch_id');

            // Si no selecciona sucursal, mostrar globales (branch_id = null)
            $categories = ComplementaryProductCategory::with('products')
                ->when($selectedBranch, function ($query, $selectedBranch) {
                    return $query->where('branch_id', $selectedBranch);
                }, function ($query) {
                    return $query->whereNull('branch_id');
                })
                ->latest()
                ->get();

            return view('admin.complementary_product.index', compact('categories', 'branches', 'selectedBranch'));
        }

        // Si es manager, solo ve las suyas
        $categories = ComplementaryProductCategory::where('branch_id', $user->branch_id)
            ->with('products')
            ->latest()
            ->get();

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
        $user = Auth::user();

        if ($request->hasFile('image')) {
            $folder = public_path('images/complementary_categories');
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/complementary_categories/' . $imageName;
        }

        // Si es manager, categoría pertenece a su sucursal
        $data['branch_id'] = $user->role->name === 'manager' ? $user->branch_id : null;

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