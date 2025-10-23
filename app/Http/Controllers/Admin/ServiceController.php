<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['category'])->orderBy('id', 'desc')->get();
        $categories = ServiceCategory::all();
        return view('admin.services.index', compact('services', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'unit_type' => 'nullable|string|max:50',
            'estimated_time' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'service_category_id',
            'name',
            'description',
            'base_price',
            'unit_type',
            'estimated_time',
        ]);

        if ($request->hasFile('image')) {
            $folder = public_path('images/services');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/services/' . $imageName;
        }

        Service::create($data);

        return redirect()->back()->with('success', 'Servicio registrado correctamente.');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'unit_type' => 'nullable|string|max:50',
            'estimated_time' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'name', 'description', 'base_price', 'unit_type', 'estimated_time'
        ]);

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($service->image && file_exists(public_path($service->image))) {
                unlink(public_path($service->image));
            }

            $folder = public_path('images/services');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move($folder, $imageName);
            $data['image'] = 'images/services/' . $imageName;
        }

        $service->update($data);

        return redirect()->back()->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Servicio eliminado correctamente.');
    }
}