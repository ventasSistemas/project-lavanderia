<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceItem;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceItemController extends Controller
{
    public function index($service_id)
    {
        $service = Service::with('items')->findOrFail($service_id);
        return view('admin.service_items.index', compact('service'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'item_name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'includes' => 'nullable|string',
            'additional_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        ServiceItem::create($request->only([
            'service_id',
            'item_name',
            'price',
            'includes',
            'additional_price',
            'notes'
        ]));

        return redirect()->back()->with('success', 'Detalle de servicio agregado correctamente.');
    }

    public function update(Request $request, ServiceItem $serviceItem)
    {
        $request->validate([
            'item_name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'includes' => 'nullable|string',
            'additional_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $serviceItem->update($request->only([
            'item_name', 'price', 'includes', 'additional_price', 'notes'
        ]));

        return redirect()->back()->with('success', 'Detalle de servicio actualizado correctamente.');
    }

    public function destroy(ServiceItem $serviceItem)
    {
        $serviceItem->delete();
        return redirect()->back()->with('success', 'Detalle de servicio eliminado correctamente.');
    }
}