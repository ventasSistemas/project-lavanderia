<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);
        
        $data = $request->only('name', 'description');

        if ($request->hasFile('image')) {
            $folder = public_path('images/service_categories');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/service_categories/' . $imageName;
        }

        ServiceCategory::create($data);

        return redirect()->back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only('name', 'description');

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($serviceCategory->image && file_exists(public_path($serviceCategory->image))) {
                unlink(public_path($serviceCategory->image));
            }

            $folder = public_path('images/service_categories');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/service_categories/' . $imageName;
        }

        $serviceCategory->update($data);

        return redirect()->back()->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return redirect()->back()->with('success', 'Categoría eliminada correctamente.');
    }
}