<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Listado de usuarios con control por rol y sucursal.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = User::with(['role', 'branch']);

        // Solo manager o subadmin ven usuarios de su sucursal
        if (in_array($user->role->name, ['manager', 'subadmin'])) {
            $query->where('branch_id', $user->branch_id);
        }

        $users = $query
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Sucursales visibles según rol
        $branches = $user->role->name === 'admin'
            ? Branch::orderBy('name')->get()
            : Branch::where('id', $user->branch_id)->get();

        // Filtrar roles según el rol del usuario logueado
        if ($user->role->name === 'admin') {
            $roles = Role::orderBy('name')->get(); // todos
        } elseif ($user->role->name === 'manager') {
            $roles = Role::where('name', 'employee')->get();
        } else {
            $roles = collect(); // sin acceso
        }

        // Traducción de roles (para mostrar en español en la vista)
        $roleTranslations = [
            'admin' => 'Administrador',
            'manager' => 'Gerente',
            'employee' => 'Empleado',
        ];

        return view('admin.users.index', compact('users', 'search', 'branches', 'roles', 'roleTranslations'));
    }

    /**
     * Crear nuevo usuario.
     */
    public function store(Request $request)
    {
        $authUser = Auth::user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:9',
            'address' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Manager solo puede asignar empleados a SU propia sucursal
        if (in_array($authUser->role->name, ['manager', 'subadmin'])) {
            $validated['branch_id'] = $authUser->branch_id;
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Actualizar usuario.
     */
    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();

        // Manager solo puede editar usuarios de su sucursal
        if (in_array($authUser->role->name, ['manager', 'subadmin']) &&
            $user->branch_id !== $authUser->branch_id) {
            return redirect()->back()->with('error', 'No tienes permiso para editar este usuario.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:9',
            'address' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Manager no puede cambiar sucursal
        if (in_array($authUser->role->name, ['manager', 'subadmin'])) {
            $validated['branch_id'] = $authUser->branch_id;
        }

        // Si se cambió la contraseña, se encripta
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario.
     
    public function destroy(User $user)
    {
        $authUser = Auth::user();

        if (in_array($authUser->role->name, ['manager', 'subadmin']) &&
            $user->branch_id !== $authUser->branch_id) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este usuario.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }*/
}
