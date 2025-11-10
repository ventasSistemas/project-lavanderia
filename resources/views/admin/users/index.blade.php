<!--views/admin/users/index..blade.php-->
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-user-tie text-primary me-2"></i> Gestión de Personales
            </h4>
            <p class="text-muted small mb-0">Administra los usuarios del sistema</p>
        </div>

        <!-- Botón Crear -->
        <button class="btn btn-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Usuario
        </button>
    </div>

    <!-- Buscador -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex align-items-center gap-2">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o email..." 
                       value="{{ request('search') }}" style="max-width: 300px;">
                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive rounded">
                <table class="table table-hover table-nowrap align-middle mb-0 rounded">
                    <thead class="table-primary text-white">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Nombre completo</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th class="pe-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td><span class="badge bg-info-subtle text-dark">{{ $roleTranslations[$user->role->name] ?? $user->role->name ?? 'N/A' }}</span></td>
                                <td><span class="badge bg-secondary-subtle text-dark">{{ $user->branch->name ?? 'N/A' }}</span></td>
                                <td>
                                    @if($user->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                    @elseif($user->status === 'inactive')
                                        <span class="badge bg-warning-subtle text-dark">Inactivo</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Suspendido</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-warning me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal{{ $user->id }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Editar Usuario -->
                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header bg-warning text-white rounded-top-4">
                                            <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Editar Usuario</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4 py-3">
                                            <form method="POST" action="{{ route('admin.users.update', ['user' => $user->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Nombre completo</label>
                                                        <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Contraseña (dejar vacía si no cambia)</label>
                                                        <input type="password" name="password" class="form-control">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Teléfono</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Dirección</label>
                                                        <textarea name="address" class="form-control" rows="2">{{ $user->address }}</textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Rol</label>
                                                        <select name="role_id" class="form-select" required>
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->id }}" 
                                                                    {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                                    {{ $roleTranslations[$role->name] ?? $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Sucursal</label>
                                                        <select name="branch_id" class="form-select">
                                                            <option value="">Ninguna</option>
                                                            @foreach(App\Models\Branch::all() as $branch)
                                                                <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Estado</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Activo</option>
                                                            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                                            <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspendido</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="text-end mt-4">
                                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                        <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-warning text-white px-4">
                                                        <i class="fa-solid fa-save me-1"></i> Actualizar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No se encontraron usuarios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="card-footer bg-white text-end">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title">+<i class="fa-solid fa-user-tie me-2"></i> Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select name="role_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $roleTranslations[$role->name] ?? $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sucursal</label>
                            <select name="branch_id" class="form-select">
                                <option value="">Ninguna</option>
                                @foreach(App\Models\Branch::all() as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                                <option value="suspended">Suspendido</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-save me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection