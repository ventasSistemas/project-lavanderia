<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Listado de clientes
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        $query = Customer::with(['user', 'branch']);

        // Si es manager, solo ve los clientes de su sucursal
        if ($user->role->name === 'manager') {
            $query->where('branch_id', $user->branch_id);
        }

        // Filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('document_number', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->orderBy('id', 'desc')->paginate(10);

        // Si es admin, obtiene todas las sucursales
        $branches = ($user->role->name === 'admin') ? Branch::orderBy('name')->get() : collect();

        $branchName = $user->branch->name ?? 'Sin sucursal asignada';

        return view('admin.customers.index', compact('customers', 'search', 'branchName', 'branches', 'user'));
    }

    /**
     * Crear nuevo cliente
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:9',
            'address' => 'nullable|string',
            'document_number' => 'nullable|string|max:50',
        ];

        // El admin puede elegir sucursal
        if ($user->role->name === 'admin') {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $validated = $request->validate($rules);

        Customer::create([
            'user_id' => $user->id,
            'branch_id' => $user->role->name === 'admin' ? $validated['branch_id'] : $user->branch_id,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'document_number' => $validated['document_number'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Cliente registrado correctamente.');
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();

        $rules = [
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:9',
            'address' => 'nullable|string',
            'document_number' => 'nullable|string|max:50',
        ];

        // Si el admin edita, puede moverlo de sucursal
        if ($user->role->name === 'admin') {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $validated = $request->validate($rules);

        $data = [
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'document_number' => $validated['document_number'] ?? null,
        ];

        if ($user->role->name === 'admin') {
            $data['branch_id'] = $validated['branch_id'];
        }

        $customer->update($data);

        return redirect()->back()->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Buscador de Clientes
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $user = Auth::user();

        $query = Customer::query()
            ->where(function ($q) use ($term) {
                $q->where('full_name', 'LIKE', "%{$term}%")
                ->orWhere('document_number', 'LIKE', "%{$term}%");
            });

        // Filtrar según el rol del usuario
        if ($user->role->name !== 'admin') {
            $query->where('branch_id', $user->branch_id); 
        }

        $customers = $query->limit(10)->get(['id', 'full_name']);

        return response()->json($customers);
    }
}