<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCombo;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceComboController extends Controller
{
    public function index()
    {
        $combos = ServiceCombo::with('services')->get();
        $services = Service::all();
        return view('admin.service_combos.index', compact('combos', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'services' => 'array|exists:services,id',
        ]);

        $combo = ServiceCombo::create($request->only(['name', 'description', 'price']));

        if ($request->has('services')) {
            $combo->services()->sync($request->services);
        }

        return redirect()->back()->with('success', 'Combo creado correctamente.');
    }

    public function update(Request $request, ServiceCombo $serviceCombo)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'services' => 'array|exists:services,id',
        ]);

        $serviceCombo->update($request->only(['name', 'description', 'price']));

        if ($request->has('services')) {
            $serviceCombo->services()->sync($request->services);
        }

        return redirect()->back()->with('success', 'Combo actualizado correctamente.');
    }

    public function destroy(ServiceCombo $serviceCombo)
    {
        $serviceCombo->delete();
        return redirect()->back()->with('success', 'Combo eliminado correctamente.');
    }
}