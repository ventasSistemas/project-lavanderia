<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    /**
     * Mostrar listado de sucursales
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = Branch::with('manager');

        // Si es un manager, solo puede ver su sucursal asignada
        if ($user->role->name === 'manager' || $user->role->name === 'subadmin') {
            $query->where('id', $user->branch_id);
        }

        $branches = $query
            ->when($search, fn($q) => 
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%"))
            ->orderBy('id', 'desc')
            ->paginate(10);

        $managerRole = Role::where('name', 'manager')->first();
        $assignedUser = $managerRole
            ? User::where('role_id', $managerRole->id)
                  ->orWhere('role_id', Role::where('name', 'subadmin')->value('id'))
                  ->orderBy('full_name')
                  ->get()
            : collect();

        return view('admin.branches.index', compact('branches', 'assignedUser', 'search'));
    }

    //Schedule
    private function processSchedule($request)
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $schedule = [];

        foreach ($days as $day) {
            if ($request->has("day_$day")) {
                $schedule[$day] = [
                    'active' => true,
                    'open' => $request->input("open_$day", '09:00'),
                    'close' => $request->input("close_$day", '19:00'),
                ];
            } else {
                $schedule[$day] = ['active' => false];
            }
        }

        return $schedule;
    }

    /**
     * Crear nueva sucursal (solo admin)
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:120|unique:branches,email',
            'manager_id' => 'nullable|exists:users,id',
            'opening_date' => 'nullable|date',
            'closing_date' => 'nullable|date|after:opening_date',
            'status' => 'required|in:active,inactive',
            'schedule' => 'nullable|json',
            'is_open' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Dentro del método store
        $branch = Branch::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'manager_id' => $request->manager_id,
            'opening_date' => $request->opening_date,
            'closing_date' => $request->closing_date,
            'status' => $request->status,
            'schedule' => $this->processSchedule($request),
            'is_open' => $request->is_open ?? false,
        ]);

        // Asignar encargado (subadmin)
        if ($request->manager_id) {
            $manager = User::find($request->manager_id);
            if ($manager) {
                $subadminRole = Role::where('name', 'subadmin')->first();
                $manager->update([
                    'role_id' => $subadminRole ? $subadminRole->id : $manager->role_id,
                    'branch_id' => $branch->id,
                ]);
            }
        }

        return redirect()->route('admin.branches.index')
            ->with('success', 'Sucursal creada correctamente.');
    }

    /**
     * Actualizar sucursal (admin o encargado de la propia)
     */
    public function update(Request $request, Branch $branch)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:120|unique:branches,email,' . $branch->id,
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'schedule' => 'nullable|json',
            'is_open' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $oldManagerId = $branch->manager_id;

        $branch->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'manager_id' => $request->manager_id,
            'status' => $request->status,
            'schedule' => $this->processSchedule($request),
            'is_open' => $request->is_open ?? $branch->is_open,
        ]);

        // Asignar nuevo manager
        if ($request->manager_id && $request->manager_id != $oldManagerId) {
            $newManager = User::find($request->manager_id);
            if ($newManager) {
                $subadminRole = Role::where('name', 'subadmin')->first();
                $newManager->update([
                    'role_id' => $subadminRole ? $subadminRole->id : $newManager->role_id,
                    'branch_id' => $branch->id,
                ]);
            }
            if ($oldManagerId) {
                User::where('id', $oldManagerId)->update(['branch_id' => null]);
            }
        }

        return redirect()->route('admin.branches.index')
            ->with('success', 'Sucursal actualizada correctamente.');
    }
}